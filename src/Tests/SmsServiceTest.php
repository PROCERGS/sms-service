<?php

namespace PROCERGS\Sms\Tests;


use Circle\RestClientBundle\Services\RestClient;
use libphonenumber\PhoneNumber;
use PROCERGS\Sms\Model\Sms;
use PROCERGS\Sms\SmsService;

class SmsServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $smsService = $this->getSmsService();

        $to = new PhoneNumber();
        $to
            ->setCountryCode(getenv('DESTINATION_PHONE_COUNTRY_CODE'))
            ->setNationalNumber(getenv('DESTINATION_PHONE_AREA_CODE').getenv('DESTINATION_PHONE_SUBSCRIBER_NUMBER'));

        $response = $this->sendSms($smsService, $to, 'sms test');
        $this->assertNotNull($response);
        $this->assertNotFalse($response);
    }

    public function testForceReceiveAll()
    {
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->expects($this->once())->method('isOk')->willReturn(true);
        $response->expects($this->once())->method('getContent')->willReturn(json_encode([['x' => 'oi']]));

        $restClient = $this->getRestClient();
        $restClient->expects($this->once())->method('get')->willReturn($response);

        $tag = 'tag';

        /** @var SmsService $smsService */
        $smsService = $this->getSmsService($restClient);

        $allSms = $smsService->forceReceive($tag);
        $this->assertNotEmpty($allSms);
    }

    public function testStatus()
    {
        $transactionId = 123456;
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->expects($this->once())->method('isOk')->willReturn(true);
        $response->expects($this->once())->method('getContent')->willReturn(
            json_encode([['numero' => $transactionId]])
        );

        $restClient = $this->getRestClient();
        $restClient->expects($this->once())->method('get')->willReturn($response);

        /** @var SmsService $smsService */
        $smsService = $this->getSmsService($restClient);

        $to = new PhoneNumber();
        $to
            ->setCountryCode(getenv('DESTINATION_PHONE_COUNTRY_CODE'))
            ->setNationalNumber(getenv('DESTINATION_PHONE_AREA_CODE').getenv('DESTINATION_PHONE_SUBSCRIBER_NUMBER'));

        $status = $smsService->getStatus($transactionId);
        $this->assertNotNull($status);
        $this->assertNotEmpty($status);

        $first = reset($status);
        $this->assertEquals($transactionId, $first->numero);
    }

    private function getRestClient()
    {
        $restClient = $this->getMockBuilder('Circle\RestClientBundle\Services\RestClient')
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->expects($this->any())->method('isOk')->willReturn(true);
        $response->expects($this->any())->method('getContent')->willReturn(
            json_encode(
                ['protocolo' => 12345678]
            )
        );

        $restClient->expects($this->any())->method('post')->willReturn($response);

        return $restClient;
    }

    /**
     * @param RestClient|null $restClient
     * @return SmsService
     */
    private function getSmsService($restClient = null)
    {
        if ($restClient === null) {
            $restClient = $this->getRestClient();
        }

        $options = [
            'send_url' => 'https://some.address/send',
            'receive_url' => 'https://some.address/receive',
            'status_url' => 'https://some.address/status',
            'system_id' => 'SYSTEM',
            'from_string' => 'SMS Service',
            'service_order' => 1234,

        ];

        return new SmsService($restClient, $options);
    }

    private function sendSms(SmsService $smsService, PhoneNumber $to, $message)
    {
        $sms = new Sms();
        $sms
            ->setFrom(getenv('TPD_SYSTEM_ID'))
            ->setTo($to)
            ->setMessage($message);

        return $smsService->send($sms);
    }
}
