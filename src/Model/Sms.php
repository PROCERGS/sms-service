<?php

namespace PROCERGS\Sms\Model;

use libphonenumber\PhoneNumber;

class Sms
{
    /** @var PhoneNumber */
    protected $to;

    /** @var string */
    protected $message;

    /** @var \DateTime */
    protected $createdAt;

    /** @var TimeConstraintInterface */
    private $deliveryTimeConstraint;

    /**
     * @return PhoneNumber
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param PhoneNumber $to
     * @return Sms
     */
    public function setTo(PhoneNumber $to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return Sms
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return Sms
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return TimeConstraintInterface
     */
    public function getDeliveryTimeConstraint()
    {
        return $this->deliveryTimeConstraint;
    }

    /**
     * @param TimeConstraintInterface $deliveryTimeConstraint
     * @return Sms
     */
    public function setDeliveryTimeConstraint($deliveryTimeConstraint)
    {
        if (!$deliveryTimeConstraint->getStartTime() instanceof TimeInterface
            || !$deliveryTimeConstraint->getEndTime() instanceof TimeInterface) {
            throw new \InvalidArgumentException('Both constraint times must be set.');
        }
        $this->deliveryTimeConstraint = $deliveryTimeConstraint;

        return $this;
    }
}
