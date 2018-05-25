<?php

namespace PROCERGS\Sms\Tests\Model;

use PROCERGS\Sms\Exception\InvalidTimeException;
use PROCERGS\Sms\Model\Time;

class TimeTest extends \PHPUnit_Framework_TestCase
{
    public function testInterfaceImplemented()
    {
        $this->assertInstanceOf('PROCERGS\Sms\Model\TimeInterface', new Time());
    }

    public function testSetValidHours()
    {
        $time = new Time();
        $hour = 0;
        while ($hour < 24) {
            $time->setHour($hour);
            $this->assertSame($hour, $time->getHour());

            $hour++;
        }
    }

    public function testSetInvalidHours()
    {
        $time = new Time();
        $invalidHours = [-1, -50, 24, 60, 120, 367, -158973];
        foreach ($invalidHours as $hour) {
            try {
                $time->setHour($hour);
                $this->fail("InvalidTimeException not thrown for invalid hour: {$hour}");
            } catch (InvalidTimeException $e) {
                continue;
            }
        }
    }

    public function testSetValidMinutes()
    {
        $time = new Time();
        $minute = 0;
        while ($minute < 60) {
            $time->setMinute($minute);
            $this->assertSame($minute, $time->getMinute());

            $minute++;
        }
    }

    public function testSetInvalidMinutes()
    {
        $time = new Time();
        $invalidMinutes = [-1, -47, 60, 120, 367, -158973];
        foreach ($invalidMinutes as $minute) {
            try {
                $time->setMinute($minute);
                $this->fail("InvalidTimeException not thrown for invalid minute: {$minute}");
            } catch (InvalidTimeException $e) {
                continue;
            }
        }
    }

    public function testFluentSetters()
    {
        $time = (new Time())
            ->setHour(10)
            ->setMinute(30);

        $this->assertSame(10, $time->getHour());
        $this->assertSame(30, $time->getMinute());
    }

    public function testConstructor()
    {
        $time = new Time(20, 37);

        $this->assertSame(20, $time->getHour());
        $this->assertSame(37, $time->getMinute());
    }

    public function testConstructorValidation()
    {
        $this->setExpectedException('PROCERGS\Sms\Exception\InvalidTimeException');

        new Time(25, 100);
    }

    public function testToString()
    {
        $this->assertInternalType('string', (string)(new Time()));
        $this->assertEquals('', (string)(new Time()));
        $this->assertEquals('10:11', (string)(new Time(10, 11)));
    }

    public function testCreateFromValidString()
    {
        $time = Time::createFromString('09:05');
        $this->assertInstanceOf('PROCERGS\Sms\Model\Time', $time);
        $this->assertEquals(9, $time->getHour());
        $this->assertEquals(5, $time->getMinute());
    }

    public function testCreateFromInvalidString()
    {
        $this->setExpectedException('PROCERGS\Sms\Exception\InvalidTimeException');
        Time::createFromString('9:5');
    }
}
