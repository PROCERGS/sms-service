<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\Sms\Model;

class TimeConstraint implements TimeConstraintInterface
{
    /** @var TimeInterface */
    private $start;

    /** @var TimeInterface */
    private $end;

    public function __construct(TimeInterface $start = null, TimeInterface $end = null)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStartTime()
    {
        return $this->start;
    }

    public function setStartTime(TimeInterface $time)
    {
        self::validateTimes($time, $this->getEndTime());
        $this->start = $time;

        return $this;
    }

    public function getEndTime()
    {
        return $this->end;
    }

    public function setEndTime(TimeInterface $time)
    {
        self::validateTimes($this->getStartTime(), $time);
        $this->end = $time;

        return $this;
    }

    public static function validateTimes(TimeInterface $start = null, TimeInterface $end = null)
    {
        if (!$start instanceof TimeInterface || !$end instanceof TimeInterface) {
            return;
        }

        $startMinutes = $start->getHour() * 60 + $start->getMinute();
        $endMinutes = $end->getHour() * 60 + $end->getMinute();

        if ($startMinutes > $endMinutes) {
            throw new \InvalidArgumentException('Start time MUST be before end time.');
        }
    }
}
