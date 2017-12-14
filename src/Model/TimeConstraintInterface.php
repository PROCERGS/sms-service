<?php

namespace PROCERGS\Sms\Model;

interface TimeConstraintInterface
{
    /**
     * TimeConstraintInterface constructor.
     * @param TimeInterface|null $start
     * @param TimeInterface|null $end
     */
    public function __construct(TimeInterface $start = null, TimeInterface $end = null);

    /**
     * @return TimeInterface
     */
    public function getStartTime();

    /**
     * @param TimeInterface $time
     * @return TimeConstraintInterface
     */
    public function setStartTime(TimeInterface $time);

    /**
     * @return TimeInterface
     */
    public function getEndTime();

    /**
     * @param TimeInterface $time
     * @return TimeConstraintInterface
     */
    public function setEndTime(TimeInterface $time);

    /**
     * If both parameters are set, checks that $start is before $end.
     * @param TimeInterface|null $start
     * @param TimeInterface|null $end
     * @return void
     */
    public static function validateTimes(TimeInterface $start = null, TimeInterface $end = null);
}
