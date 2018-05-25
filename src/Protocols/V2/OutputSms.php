<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\Sms\Protocols\V2;

use PROCERGS\Sms\Exception\InvalidTimeException;
use PROCERGS\Sms\Model\Time;
use PROCERGS\Sms\Model\TimeConstraint;
use PROCERGS\Sms\Protocols\ResponseInterface;
use PROCERGS\Sms\Protocols\SmsInterface;

final class OutputSms implements ResponseInterface
{
    /**
     * @param array $payload
     * @return SmsInterface
     */
    public function receive(array $payload)
    {
        $expectedFormat = [
            'to' => null,
            'text' => null,
            'beginTime' => null,
            'endTime' => null,
            'send' => null,
            'id' => null,
            'sendDate' => null,
            'deliveryDate' => null,
            'status' => null,
            'statusDetails' => null,
        ];

        $payload = array_merge($expectedFormat, $payload);
        $builder = (new SmsBuilder($payload['to'], $payload['text']))
            ->setDeliveryTimeConstraint(new TimeConstraint(
                Time::createFromString($payload['beginTime']),
                Time::createFromString($payload['endTime'])
            ))
            ->setShouldSend(is_string($payload['send']) ? $payload['send'] === 'true' : $payload['send'])
            ->setId($payload['id'])
            ->setSendDate($this->parseDateTime($payload['sendDate']))
            ->setDeliveryDate($this->parseDateTime($payload['deliveryDate']))
            ->setStatus($payload['status'])
            ->setStatusDetails($payload['statusDetails']);

        return $builder->build();
    }

    /**
     * @param string|null $string
     * @return \DateTime|null
     */
    private function parseDateTime($string = null)
    {
        if (is_null($string)) {
            return null;
        }

        $javaFormat = 'Y-m-d\TH:i:s.uP';
        $dateTime = \DateTime::createFromFormat($javaFormat, $string);

        if (!$dateTime instanceof \DateTime) {
            throw new InvalidTimeException("Could not parse timestamp '{$string}'.");
        }

        return $dateTime;
    }
}
