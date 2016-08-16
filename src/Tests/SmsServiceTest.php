<?php

namespace PROCERGS\Sms\Tests;


use PROCERGS\Sms\Model\PhoneNumber;
use PROCERGS\Sms\Model\Sms;
use PROCERGS\Sms\SmsService;

class SmsServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $to = new PhoneNumber();
        $to
            ->setCountryCode(getenv('DESTINATION_PHONE_COUNTRY_CODE'))
            ->setAreaCode(getenv('DESTINATION_PHONE_AREA_CODE'))
            ->setSubscriberNumber(getenv('DESTINATION_PHONE_SUBSCRIBER_NUMBER'));

        $response = $this->sendSms($to, 'sms test');
        $this->assertNotNull($response);
        $this->assertNotFalse($response);
        $this->assertTrue(is_string($response));
    }

    public function testForceReceiveAll()
    {
        $tag = getenv('SMS_TAG');

        /** @var SmsService $smsService */
        $smsService = $this->container->get('sms.service');

        $allSms = $smsService->forceReceive($tag);
        $this->assertNotEmpty($allSms);
        $lastSms = end($allSms);

        $smsQueue = $smsService->forceReceive($tag, $lastSms->id);
        $this->assertEmpty($smsQueue);
    }

    public function testStatus()
    {
        /** @var SmsService $smsService */
        $smsService = $this->container->get('sms.service');

        $to = new PhoneNumber();
        $to
            ->setAreaCode(getenv('FROM_PHONE_COUNTRY_CODE'))
            ->setSubscriberNumber(getenv('FROM_PHONE_SUBSCRIBER_NUMBER'));
        $transactionId = $this->sendSms($to, 'testing status');

        $status = $smsService->getStatus($transactionId);
        $this->assertNotNull($status);
        $this->assertNotEmpty($status);

        $first = reset($status);
        $this->assertEquals($transactionId, $first->numero);
    }

    private function sendSms(PhoneNumber $to, $message)
    {
        /** @var SmsService $smsService */
        $smsService = $this->container->get('sms.service');

        $sms = new Sms();
        $sms
            ->setFrom(getenv('TPD_SYSTEM_ID'))
            ->setTo($to)
            ->setMessage($message);

        return $smsService->send($sms);
    }
}
