<?php

namespace PROCERGS\Sms\Tests\Protocols\V2;

use PROCERGS\Sms\Protocols\V2\SmsBuilder;

class SmsBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testValidStatus()
    {
        $valid = [
            'WAITING_TO_BE_SENT',
            'ERROR_IN_SEND',
            'WAITING_TO_BE_DELIVERED',
            'DELIVERED',
            'ERROR_IN_DELIVERY',
            'NO_DELIVERY_CONFIRMATION',
            'NOT_SEND',
        ];
        $builder = new SmsBuilder('51999999999', 'my text');

        foreach ($valid as $status) {
            $builder->setStatus($status);
            $this->assertSame($status, $builder->getStatus()->getStatusCode());
        }
    }

    public function testInvalidStatus()
    {
        $this->setExpectedException('PROCERGS\Sms\Exception\InvalidStatusException');

        (new SmsBuilder('51999999999', 'my text'))
            ->setStatus('invalid status here');
    }

    public function testNotMobile()
    {
        $this->setExpectedException('PROCERGS\Sms\Exception\InvalidPhoneNumberException');

        new SmsBuilder('5133333333', 'my text');
    }

    public function testInvalidPhone()
    {
        $this->setExpectedException('PROCERGS\Sms\Exception\InvalidPhoneNumberException');

        new SmsBuilder('potato', 'my text');
    }
}
