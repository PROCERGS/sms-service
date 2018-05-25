<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\Sms\Tests\Protocols\V2;

use PROCERGS\Sms\Model\Time;
use PROCERGS\Sms\Model\TimeConstraint;
use PROCERGS\Sms\Protocols\V2\InputSms;
use PROCERGS\Sms\Protocols\V2\SmsBuilder;

class InputSmsTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleSms()
    {
        $sms = (new SmsBuilder('51999999999', 'my message'))->build();
        $input = (new InputSms($sms))->getPayload();

        $this->assertArrayHasKey('to', $input);
        $this->assertArrayHasKey('text', $input);
        $this->assertArrayNotHasKey('send', $input);
        $this->assertArrayNotHasKey('beginTime', $input);
        $this->assertArrayNotHasKey('endTime', $input);

        $this->assertSame('51999999999', $input['to']);
        $this->assertSame('my message', $input['text']);
    }

    public function testSimpleSmsWithSend()
    {
        $sms = (new SmsBuilder('51999999999', 'my message'))
            ->setShouldSend(true)->build();
        $input = (new InputSms($sms))->getPayload();

        $this->assertArrayHasKey('to', $input);
        $this->assertArrayHasKey('text', $input);
        $this->assertArrayHasKey('send', $input);
        $this->assertArrayNotHasKey('beginTime', $input);
        $this->assertArrayNotHasKey('endTime', $input);

        $this->assertSame('51999999999', $input['to']);
        $this->assertSame('my message', $input['text']);
        $this->assertTrue($input['send']);
    }

    public function testFullSms()
    {
        $sms = (new SmsBuilder('51999999999', 'my message'))
            ->setDeliveryTimeConstraint(new TimeConstraint(
                Time::createFromString('09:00'),
                Time::createFromString('18:00')
            ))
            ->setShouldSend(true)
            ->build();
        $input = (new InputSms($sms))->getPayload();

        $this->assertArrayHasKey('to', $input);
        $this->assertArrayHasKey('text', $input);
        $this->assertArrayHasKey('send', $input);
        $this->assertArrayHasKey('beginTime', $input);
        $this->assertArrayHasKey('endTime', $input);

        $this->assertSame('51999999999', $input['to']);
        $this->assertSame('my message', $input['text']);
        $this->assertSame('09:00', $input['beginTime']);
        $this->assertSame('18:00', $input['endTime']);
        $this->assertTrue($input['send']);

        $this->assertSame($input, (new InputSms($sms))->jsonSerialize());
    }
}
