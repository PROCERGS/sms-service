<?php

namespace PROCERGS\Sms\Model;

interface TimeInterface
{
    public function __construct($hour = null, $minute = null);

    /**
     * @return int
     */
    public function getHour();

    /**
     * @param int $hour
     * @return TimeInterface
     */
    public function setHour($hour);

    /**
     * @return int
     */
    public function getMinute();

    /**
     * @param int $minute
     * @return TimeInterface
     */
    public function setMinute($minute);

    /**
     * @return string
     */
    public function __toString();
}
