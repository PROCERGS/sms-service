<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\Sms\Protocols\V2;

use libphonenumber\PhoneNumber;
use PROCERGS\Sms\Model\Time;
use PROCERGS\Sms\Model\TimeConstraint;
use PROCERGS\Sms\Model\TimeConstraintInterface;
use PROCERGS\Sms\Protocols\SmsInterface;
use PROCERGS\Sms\Protocols\SmsStatusInterface;

final class Sms implements SmsInterface
{
    /** @var PhoneNumber */
    private $to;

    /** @var string */
    private $text;

    /** @var string */
    private $beginTime;

    /** @var string */
    private $endTime;

    /** @var bool */
    private $shouldSend;

    /** @var int */
    private $id;

    /** @var SmsStatusInterface */
    private $status;

    /**
     * @param PhoneNumber $to
     * @param string $message
     * @param bool $shouldSend
     * @return SmsInterface
     */
    public static function createSimpleSms(PhoneNumber $to, $message, $shouldSend = true)
    {
        $builder = (new SmsBuilder($to, $message))
            ->setShouldSend($shouldSend);

        return new self($builder);
    }

    /**
     * Sms constructor.
     * @param SmsBuilder $builder
     */
    public function __construct(SmsBuilder $builder)
    {
        $this->to = $builder->getTo();
        $this->text = $builder->getText();
        $this->beginTime = $builder->getDeliveryTimeConstraint()->getStartTime();
        $this->endTime = $builder->getDeliveryTimeConstraint()->getEndTime();
        $this->shouldSend = $builder->isShouldSend();
        $this->id = $builder->getId();
        $this->status = $builder->getStatus();
    }

    /**
     * @return PhoneNumber
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getBeginTime()
    {
        return $this->beginTime;
    }

    /**
     * @return string
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return bool
     */
    public function isShouldSend()
    {
        return $this->shouldSend;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return SmsStatusInterface
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->getText();
    }

    /**
     * @return TimeConstraintInterface
     */
    public function getDeliveryTimeConstraint()
    {
        return new TimeConstraint(
            Time::createFromString($this->getBeginTime()),
            Time::createFromString($this->getEndTime())
        );
    }
}
