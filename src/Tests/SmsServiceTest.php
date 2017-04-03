<?php

namespace PROCERGS\Sms\Tests;


use Circle\RestClientBundle\Services\RestClient;
use libphonenumber\PhoneNumber;
use PROCERGS\Sms\SmsService;

class SmsServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $smsService = $this->getSmsService();

        $to = $this->getValidPhoneNumber();

        $response = $this->sendSms($smsService, $to, 'sms test');
        $this->assertNotNull($response);
        $this->assertNotFalse($response);
    }

    public function testSendInvalidPhone()
    {
        $smsService = $this->getSmsService();

        $to = $this->getMock('libphonenumber\PhoneNumber');
        $to->expects($this->once())->method('getCountryCode')->willReturn('1');
        $this->setExpectedException('PROCERGS\Sms\Exception\InvalidCountryException');

        $this->sendSms($smsService, $to, "this should fail");
    }

    public function testSendInvalidPhone2()
    {
        $smsService = $this->getSmsService();

        $to = $this->getMock('libphonenumber\PhoneNumber');
        $to->expects($this->atLeastOnce())->method('getCountryCode')->willReturn('55');
        $to->expects($this->atLeastOnce())->method('getNationalNumber')->willReturn('1');
        $this->setExpectedException('PROCERGS\Sms\Exception\InvalidPhoneNumberException');

        $this->sendSms($smsService, $to, "this should fail");
    }

    public function testSendFailureJson()
    {
        $this->setExpectedException('PROCERGS\Sms\Exception\SmsServiceException');
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->expects($this->once())->method('isOk')->willReturn(false);
        $response->expects($this->atLeastOnce())->method('getContent')->willReturn(
            json_encode([['error' => 'message']])
        );

        $restClient = $this->getRestClient($response);

        $smsService = $this->getSmsService($restClient);
        $this->sendSms($smsService, $this->getValidPhoneNumber(), "this should fail");
    }

    public function testSendFailureString()
    {
        $this->setExpectedException('PROCERGS\Sms\Exception\SmsServiceException');
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->expects($this->once())->method('isOk')->willReturn(false);
        $response->expects($this->atLeastOnce())->method('getContent')->willReturn('error');

        $restClient = $this->getRestClient($response);

        $smsService = $this->getSmsService($restClient);
        $this->sendSms($smsService, $this->getValidPhoneNumber(), "this should fail");
    }

    public function testForceReceive()
    {
        $messages = [
            [
                "id" => 123,
                "de" => "5551999999999",
                "para" => "666",
                "mensagem" => "Message 1",
                "dataHoraRecebimento" => "2016-07-07T15:57:35.000-03:00",
            ],
            [
                "id" => 321,
                "de" => "5554999999999",
                "para" => "666",
                "mensagem" => "Message 2",
                "dataHoraRecebimento" => "2016-11-23T14:23:02.000-03:00",
            ],
        ];

        $lastId = 123;
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->expects($this->once())->method('isOk')->willReturn(true);
        $response->expects($this->once())->method('getContent')->willReturn(json_encode($messages));

        $restClient = $this->getRestClient();
        $restClient->expects($this->once())->method('get')->willReturn($response);

        $tag = 'tag';

        /** @var SmsService $smsService */
        $smsService = $this->getSmsService($restClient);

        $received = $smsService->forceReceive($tag, $lastId);
        $this->assertNotEmpty($received);
        $this->assertCount(count($messages), $received);
    }

    public function testForceReceiveError()
    {
        $this->setExpectedException('PROCERGS\Sms\Exception\SmsServiceException');

        $lastId = 123;
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->expects($this->once())->method('isOk')->willReturn(true);
        $response->expects($this->atLeastOnce())->method('getContent')->willReturn('error');

        $restClient = $this->getRestClient();
        $restClient->expects($this->once())->method('get')->willReturn($response);

        $tag = 'tag';

        /** @var SmsService $smsService */
        $smsService = $this->getSmsService($restClient);

        $smsService->forceReceive($tag, $lastId);
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

    public function testStatusError()
    {
        $errorResponse = json_encode(
            [
                [
                    "id" => null,
                    "message" => "Protocolo 'a' inválido, deve conter apenas números.",
                    "field" => null,
                    "detail" => null,
                ],
            ]
        );
        $error = json_decode($errorResponse);
        $expectedMessage = reset($error)->message;

        $this->setExpectedException('PROCERGS\Sms\Exception\SmsServiceException', $expectedMessage);

        $transactionId = 'error';
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->expects($this->once())->method('isOk')->willReturn(false);
        $response->expects($this->once())->method('getContent')->willReturn($errorResponse);

        $restClient = $this->getRestClient();
        $restClient->expects($this->once())->method('get')->willReturn($response);

        /** @var SmsService $smsService */
        $smsService = $this->getSmsService($restClient);
        $smsService->getStatus($transactionId);
    }

    private function getRestClient($response = null)
    {
        $restClient = $this->getMockBuilder('Circle\RestClientBundle\Services\RestClient')
            ->disableOriginalConstructor()
            ->getMock();

        if (!$response) {
            $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
            $response->expects($this->any())->method('isOk')->willReturn(true);
            $response->expects($this->any())->method('getContent')->willReturn(
                json_encode(
                    ['protocolo' => 12345678]
                )
            );
        }

        $restClient->expects($this->any())->method('post')->willReturn($response);

        return $restClient;
    }

    /**
     * @param RestClient|null $restClient
     * @return SmsService
     */
    private function getSmsService($restClient = null)
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
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
            'authentication' => [
                'system_id' => 'SOME_ID',
                'system_key' => 'SECRET_KEY',
            ],
        ];

        $service = new SmsService($restClient, $options);
        $service->setLogger($logger);

        return $service;
    }

    private function sendSms(SmsService $smsService, PhoneNumber $to, $message)
    {
        return $smsService->easySend($to, $message);
    }

    private function getValidPhoneNumber()
    {
        $phoneNumber = new PhoneNumber();
        $phoneNumber
            ->setCountryCode(getenv('DESTINATION_PHONE_COUNTRY_CODE'))
            ->setNationalNumber(getenv('DESTINATION_PHONE_AREA_CODE').getenv('DESTINATION_PHONE_SUBSCRIBER_NUMBER'));

        return $phoneNumber;
    }
}
