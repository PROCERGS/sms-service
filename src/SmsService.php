<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\Sms;

use Circle\RestClientBundle\Exceptions\CurlException;
use Circle\RestClientBundle\Services\RestClient;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use PROCERGS\Sms\Exception\InvalidCountryException;
use PROCERGS\Sms\Exception\InvalidPhoneNumberException;
use PROCERGS\Sms\Model\Sms;
use PROCERGS\Sms\Exception\SmsServiceException;
use PROCERGS\Sms\Model\SmsServiceConfiguration;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class SmsService implements LoggerAwareInterface
{
    use LoggerAwareTrait, LoggerTrait;

    /** @var RestClient */
    protected $restClient;

    /** @var SmsServiceConfiguration */
    protected $config;

    /**
     * SmsService constructor.
     * @param RestClient $restClient
     * @param SmsServiceConfiguration $config
     */
    public function __construct(RestClient $restClient, SmsServiceConfiguration $config)
    {
        $this->restClient = $restClient;
        $this->config = $config;
    }

    /**
     * Sends an SMS and returns the id for later checking.
     * @param Sms $sms
     * @return string transaction id for later checking
     * @throws ServiceUnavailableHttpException
     */
    public function send(Sms $sms)
    {
        $client = $this->restClient;

        if ($sms->getTo()->getCountryCode() != 55) {
            throw new InvalidCountryException("SMS Service can only send messages to Brazil");
        } else {
            $to = $this->parseBrazilianPhone($sms->getTo());
        }

        $payload = json_encode(
            [
                'aplicacao' => $this->config->getSystemId(),
                'ordemServico' => $this->config->getServiceOrder(),
                'remetente' => $sms->getFrom(),
                'texto' => $sms->getMessage(),
                'ddd' => $to['area_code'],
                'numero' => $to['subscriber_number'],
                'send' => $this->config->shouldSend(),
            ]
        );

        $this->info("Sending SMS to {$to['e164']}: {$sms->getMessage()}");
        $response = $client->post($this->config->getSendUri(), $payload, $this->getHeaders());
        $json = json_decode($response->getContent());
        if ($response->isOk() && property_exists($json, 'protocolo')) {
            $this->info("SMS sent to {$to['e164']}: {$sms->getMessage()}");

            return $json->protocolo;
        } else {
            $this->error("Error sending SMS to {$to['e164']}");

            return $this->handleException($response, $json);
        }
    }

    /**
     * Force fetch pending SMS messages
     * @param $tag string "tag" to be fetched
     * @param integer $lastId
     * @return array
     * @throws CurlException
     * @throws SmsServiceException
     */
    public function forceReceive($tag, $lastId = null)
    {
        $client = $this->restClient;

        $params = compact('tag');
        if ($lastId !== null) {
            $params['ultimoId'] = $lastId;
        }

        $this->info("Fetching SMS for tag $tag...");
        $response = $client->get($this->config->getReceiveUri()."?".http_build_query($params), $this->getHeaders());
        $json = json_decode($response->getContent());
        if ($response->isOk() && $json !== null && is_array($json)) {
            usort(
                $json,
                function ($a, $b) {
                    return $a->id - $b->id;
                }
            );
        } else {
            return $this->handleException($response, $json);
        }

        return $json;
    }

    /**
     * @param Response $response
     * @param mixed $json
     * @throws SmsServiceException
     */
    private function handleException(Response $response, $json = null)
    {
        if ($json === null) {
            $json = json_decode($response->getContent());
        }
        if (is_array($json)) {
            throw new SmsServiceException($json, $response->getStatusCode());
        } else {
            throw new SmsServiceException($response->getContent(), $response->getStatusCode());
        }
    }

    /**
     * Checks the status of sent messages
     * @param $transactionId mixed
     * @return array
     * @throws CurlException
     * @throws SmsServiceException
     */
    public function getStatus($transactionId)
    {
        $transactionIds = is_array($transactionId) ? $transactionId : [$transactionId];

        $client = $this->restClient;
        $response = $client->get(
            $this->config->getStatusUri().'?protocolos='.implode(',', $transactionIds),
            $this->getHeaders()
        );
        $json = json_decode($response->getContent());
        if ($response->isOk() && $json !== null && is_array($json)) {
            return $json;
        } else {
            return $this->handleException($response, $json);
        }
    }

    /**
     * @param PhoneNumber $to
     * @param string $message
     * @return string
     */
    public function easySend(PhoneNumber $to, $message)
    {
        $sms = new Sms();
        $sms
            ->setFrom($this->config->getFrom())
            ->setTo($to)
            ->setMessage($message);

        return $this->send($sms);
    }

    private function parseBrazilianPhone(PhoneNumber $phoneNumber)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $e164 = $phoneUtil->format($phoneNumber, PhoneNumberFormat::E164);

        $m = null;
        if (preg_match('/^[+]55(\d{2})(\d+)$/', $e164, $m) <= 0) {
            throw new InvalidPhoneNumberException("The provided phone number does not seem to be Brazilian.");
        }

        return [
            'area_code' => $m[1],
            'subscriber_number' => $m[2],
            'e164' => $e164,
        ];
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    protected function log($level, $message, array $context = array())
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    private function getHeaders()
    {
        return [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "sistema: ".$this->config->getSystemId(),
                "chave: ".$this->config->getSystemKey(),
            ],
        ];
    }
}
