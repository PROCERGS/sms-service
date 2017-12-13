<?php

namespace PROCERGS\Sms\Tests;

use Circle\RestClientBundle\Services\RestClient;
use libphonenumber\PhoneNumber;
use PROCERGS\Sms\Model\Sms;
use PROCERGS\Sms\Model\SmsServiceConfiguration;
use PROCERGS\Sms\Model\Time;
use PROCERGS\Sms\Model\TimeConstraint;
use PROCERGS\Sms\Model\TimeConstraintInterface;
use PROCERGS\Sms\SmsService;
use Symfony\Component\HttpFoundation\Response;

class SmsServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $id = 12345678;
        $httpResponse = $this->getResponse();
        $httpResponse->expects($this->any())->method('isOk')->willReturn(true);
        $httpResponse->expects($this->any())->method('getContent')->willReturn(json_encode($id));

        $restClient = $this->getRestClient();
        $restClient->expects($this->once())->method('post')
            ->with(
                $this->isType('string'),
                $this->logicalAnd(
                    $this->stringContains('"to":'),
                    $this->stringContains('"text":'),
                    $this->stringContains('"send":')
                ),
                $this->getExpectAuthorization()
            )
            ->willReturn($httpResponse);

        $smsService = $this->getSmsService($restClient);

        $to = $this->getValidPhoneNumber();

        $response = $this->sendSms($smsService, $to, 'sms test');
        $this->assertNotNull($response);
        $this->assertNotFalse($response);
        $this->assertEquals($id, $response);
    }

    public function testSendWithTimeConstraints()
    {
        $id = 12345678;
        $httpResponse = $this->getResponse();
        $httpResponse->expects($this->any())->method('isOk')->willReturn(true);
        $httpResponse->expects($this->any())->method('getContent')->willReturn(json_encode($id));

        $restClient = $this->getRestClient();
        $restClient->expects($this->once())->method('post')
            ->with(
                $this->isType('string'),
                $this->logicalAnd(
                    $this->stringContains('"to":', false),
                    $this->stringContains('"text":', false),
                    $this->stringContains('"send":', false),
                    $this->stringContains('"beginTime":', false),
                    $this->stringContains('"endTime":', false)
                ),
                $this->getExpectAuthorization()
            )
            ->willReturn($httpResponse);

        $smsService = $this->getSmsService($restClient);

        /** @var TimeConstraintInterface $timeConstraint */
        $timeConstraint = (new TimeConstraint())
            ->setStartTime(new Time())
            ->setEndTime(new Time());

        $sms = (new Sms())
            ->setDeliveryTimeConstraint($timeConstraint)
            ->setTo($this->getValidPhoneNumber())
            ->setMessage('sms test');

        $response = $smsService->send($sms);
        $this->assertNotNull($response);
        $this->assertNotFalse($response);
        $this->assertEquals($id, $response);
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

        $restClient = $this->getSimpleRestClient($response);

        $smsService = $this->getSmsService($restClient);
        $this->sendSms($smsService, $this->getValidPhoneNumber(), "this should fail");
    }

    public function testSendFailureString()
    {
        $this->setExpectedException('PROCERGS\Sms\Exception\SmsServiceException');
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->expects($this->once())->method('isOk')->willReturn(false);
        $response->expects($this->atLeastOnce())->method('getContent')->willReturn('error');

        $restClient = $this->getSimpleRestClient($response);

        $smsService = $this->getSmsService($restClient);
        $this->sendSms($smsService, $this->getValidPhoneNumber(), "this should fail");
    }

    public function testForceReceive()
    {
        $messages = [
            [
                "id" => 123,
                "from" => "51999999999",
                "to" => "666",
                "text" => "Message 1",
                "date" => "2017-07-31T22:09:00.977-02:00",
            ],
            [
                "id" => 321,
                "from" => "5554999999999",
                "to" => "666",
                "text" => "Message 2",
                "date" => "2016-11-23T14:23:02.000-03:00",
            ],
        ];

        $tag = 'tag';
        $lastId = 123;
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->expects($this->once())->method('isOk')->willReturn(true);
        $response->expects($this->once())->method('getContent')->willReturn(json_encode($messages));

        $restClient = $this->getSimpleRestClient();
        $restClient->expects($this->once())->method('get')
            ->with(
                $this->logicalAnd(
                    $this->isType('string'),
                    $this->stringContains("tag={$tag}"),
                    $this->stringContains("firstId={$lastId}")
                ),
                $this->getExpectAuthorization()
            )
            ->willReturn($response);


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

        $restClient = $this->getSimpleRestClient();
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
            json_encode([
                'to' => '51999999999',
                'text' => 'example text',
                'send' => true,
                'id' => $transactionId,
                'sendDate' => '2017-09-13T13:00:36.000-02:00',
                'deliveryDate' => '2017-09-13T13:00:41.503-02:00',
                'status' => 'DELIVERED',
                'statusDetails' => 'Message delivered to handset',
            ])
        );

        $restClient = $this->getRestClient();
        $restClient->expects($this->once())->method('get')
            ->with(
                $this->logicalAnd(
                    $this->stringContains((string)$transactionId)
                ),
                $this->getExpectAuthorization()
            )
            ->willReturn($response);

        /** @var SmsService $smsService */
        $smsService = $this->getSmsService($restClient);

        $status = $smsService->getStatus($transactionId);
        $this->assertNotNull($status);
        $this->assertEquals($transactionId, $status->id);
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

        $restClient = $this->getSimpleRestClient();
        $restClient->expects($this->once())->method('get')->willReturn($response);

        /** @var SmsService $smsService */
        $smsService = $this->getSmsService($restClient);
        $smsService->getStatus($transactionId);
    }

    private function getSimpleRestClient($response = null)
    {
        $restClient = $this->getRestClient();

        if (!$response) {
            $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
            $response->expects($this->any())->method('isOk')->willReturn(true);
            $response->expects($this->any())->method('getContent')->willReturn(
                json_encode(12345678)
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
            $restClient = $this->getSimpleRestClient();
        }

        $config = new SmsServiceConfiguration(
            'https://some.address/send',
            'https://some.address/receive',
            'https://some.address/status/{id}',
            'REALM',
            'SYSTEM',
            'SECRET_KEY',
            false
        );

        $service = new SmsService($restClient, $config);
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
            ->setCountryCode('55')
            ->setNationalNumber('55999999999');

        return $phoneNumber;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RestClient
     */
    private function getRestClient()
    {
        return $this->getMockBuilder('Circle\RestClientBundle\Services\RestClient')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Response
     */
    private function getResponse()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Response');
    }

    private function getExpectAuthorization()
    {
        return $this->equalTo([
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "organizacao: REALM",
                "matricula: SYSTEM",
                "senha: SECRET_KEY",
            ],
        ]);
    }
}
