<?php

namespace PROCERGS\Sms\Tests\Model;

use libphonenumber\PhoneNumber;
use PROCERGS\Sms\Model\Sms;

class SmsTest extends \PHPUnit_Framework_TestCase
{
    public function testSms()
    {
        /** @var PhoneNumber $phoneNumber */
        $phoneNumber = $this->getMock('libphonenumber\PhoneNumber');
        $from = 'FROM';
        $message = 'SOME MESSAGE';
        $createdAt = new \DateTime();
        $deliveryStartDate = new \DateTime();
        $deliveryEndDate = new \DateTime();

        $sms = new Sms();
        $sms->setCreatedAt($createdAt);
        $sms->setFrom($from);
        $sms->setMessage($message);
        $sms->setTo($phoneNumber);
        $sms->setDontDeliverUntil($deliveryStartDate);
        $sms->setDontDeliverAfter($deliveryEndDate);

        $this->assertSame($createdAt, $sms->getCreatedAt());
        $this->assertEquals($from, $sms->getFrom());
        $this->assertEquals($message, $sms->getMessage());
        $this->assertEquals($phoneNumber, $sms->getTo());
        $this->assertSame($deliveryStartDate, $sms->getDontDeliverUntil());
        $this->assertSame($deliveryEndDate, $sms->getDontDeliverAfter());
    }
}
