<?php

namespace PROCERGS\Sms\Tests\Model;

use libphonenumber\PhoneNumber;
use PROCERGS\Sms\Model\Sms;
use PROCERGS\Sms\Model\Time;
use PROCERGS\Sms\Model\TimeConstraint;

class SmsTest extends \PHPUnit_Framework_TestCase
{
    public function testSms()
    {
        /** @var PhoneNumber $phoneNumber */
        $phoneNumber = $this->getMock('libphonenumber\PhoneNumber');
        $message = 'SOME MESSAGE';
        $createdAt = new \DateTime();
        $deliveryStartDate = new Time(23, 00);
        $deliveryEndDate = new Time(23, 58);

        $sms = new Sms();
        $sms->setCreatedAt($createdAt);
        $sms->setMessage($message);
        $sms->setTo($phoneNumber);
        $sms->setDeliveryTimeConstraint(new TimeConstraint($deliveryStartDate, $deliveryEndDate));

        $this->assertSame($createdAt, $sms->getCreatedAt());
        $this->assertEquals($message, $sms->getMessage());
        $this->assertEquals($phoneNumber, $sms->getTo());
        $this->assertSame($deliveryStartDate, $sms->getDeliveryTimeConstraint()->getStartTime());
        $this->assertSame($deliveryEndDate, $sms->getDeliveryTimeConstraint()->getEndTime());
    }

    public function testTimeConstraintMustHaveAStartTime()
    {
        $validConstraint = new TimeConstraint(new Time(01, 10), new Time(02, 20));
        $invalidConstraint = new TimeConstraint(null, new Time(02, 20));

        $sms = new Sms();
        $sms->setDeliveryTimeConstraint($validConstraint);
        $this->assertSame($validConstraint, $sms->getDeliveryTimeConstraint());

        $this->setExpectedException('\InvalidArgumentException');
        $sms->setDeliveryTimeConstraint($invalidConstraint);
    }

    public function testTimeConstraintMustHaveAnEndTime()
    {
        $validConstraint = new TimeConstraint(new Time(01, 10), new Time(02, 20));
        $invalidConstraint = new TimeConstraint(new Time(01, 10), null);

        $sms = new Sms();
        $sms->setDeliveryTimeConstraint($validConstraint);
        $this->assertSame($validConstraint, $sms->getDeliveryTimeConstraint());

        $this->setExpectedException('\InvalidArgumentException');
        $sms->setDeliveryTimeConstraint($invalidConstraint);
    }
}
