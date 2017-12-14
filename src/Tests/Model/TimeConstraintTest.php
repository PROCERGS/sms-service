<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\Sms\Tests\Model;

use PROCERGS\Sms\Model\Time;
use PROCERGS\Sms\Model\TimeConstraint;

class TimeConstraintTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsInterface()
    {
        $this->assertInstanceOf('PROCERGS\Sms\Model\TimeConstraintInterface', new TimeConstraint());
    }

    public function testConstructor()
    {
        $start = $this->getMock('PROCERGS\Sms\Model\TimeInterface');
        $end = $this->getMock('PROCERGS\Sms\Model\TimeInterface');

        $constraint = new TimeConstraint($start, $end);

        $this->assertSame($start, $constraint->getStartTime());
        $this->assertSame($end, $constraint->getEndTime());
    }

    public function testFluentSetters()
    {
        $start = $this->getMock('PROCERGS\Sms\Model\TimeInterface');
        $end = $this->getMock('PROCERGS\Sms\Model\TimeInterface');

        $constraint = (new TimeConstraint())
            ->setStartTime($start)
            ->setEndTime($end);

        $this->assertSame($start, $constraint->getStartTime());
        $this->assertSame($end, $constraint->getEndTime());
    }

    /**
     * Start time MUST NOT be after end time
     */
    public function testStartTimeAfterEndTime()
    {
        $constraint = new TimeConstraint();

        $start = new Time(10, 30);
        $end = new Time(11, 0);

        $constraint->setStartTime($end);
        try {
            $constraint->setEndTime($start);
            $this->fail("Start time MUST NOT be after end time");
        } catch (\InvalidArgumentException $e) {
            //
        }
    }

    /**
     * End time MUST NOT be before start time
     */
    public function testEndTimeBeforeStartTime()
    {
        $constraint = new TimeConstraint();

        $start = new Time(10, 30);
        $end = new Time(11, 0);

        $constraint->setEndTime($start);
        try {
            $constraint->setStartTime($end);
            $this->fail("End time MUST NOT be before start time");
        } catch (\InvalidArgumentException $e) {
            //
        }
    }

    public function testValidateTimesDoesNothingOnNull()
    {
        TimeConstraint::validateTimes(null, null);
        TimeConstraint::validateTimes(null, new Time());
        TimeConstraint::validateTimes(new Time(), null);
    }

    public function testValidateTimesAllowValid()
    {
        TimeConstraint::validateTimes(new Time(1, 0), new Time(1, 1));
    }
}
