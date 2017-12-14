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

class Time implements TimeInterface
{
    /** @var int */
    private $hour;

    /** @var int */
    private $minute;

    public function __construct($hour = null, $minute = null)
    {
        if ($hour !== null) {
            $this->setHour($hour);
        }
        if ($minute !== null) {
            $this->setMinute($minute);
        }
    }

    /**
     * @return int
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * @param int $hour
     * @return TimeInterface
     */
    public function setHour($hour)
    {
        if ($hour < 0 || $hour >= 24) {
            throw new \InvalidArgumentException("{$hour} is not a valid value");
        }
        $this->hour = $hour;

        return $this;
    }

    /**
     * @return int
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * @param int $minute
     * @return TimeInterface
     */
    public function setMinute($minute)
    {
        if ($minute < 0 || $minute >= 60) {
            throw new \InvalidArgumentException("{$minute} is not a valid value");
        }
        $this->minute = $minute;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getHour() === null || $this->getMinute() === null) {
            return '';
        }

        return sprintf('%s:%s', str_pad($this->getHour(), 2, '0'), str_pad($this->getMinute(), 2, '0'));
    }
}
