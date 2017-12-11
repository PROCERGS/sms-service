<?php

namespace PROCERGS\Sms\Model;

use libphonenumber\PhoneNumber;

class Sms
{
    /** @var PhoneNumber */
    protected $to;

    /** @var PhoneNumber */
    protected $from;

    /** @var string */
    protected $message;

    /** @var \DateTime */
    protected $createdAt;

    /** @var \DateTime */
    private $dontDeliverUntil;

    /** @var \DateTime */
    private $dontDeliverAfter;

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
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     * @return Sms
     */
    public function setFrom($from)
    {
        $this->from = $from;

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
     * @return \DateTime
     */
    public function getDontDeliverUntil()
    {
        return $this->dontDeliverUntil;
    }

    /**
     * @param \DateTime $dontDeliverUntil
     * @return Sms
     */
    public function setDontDeliverUntil($dontDeliverUntil)
    {
        $this->dontDeliverUntil = $dontDeliverUntil;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDontDeliverAfter()
    {
        return $this->dontDeliverAfter;
    }

    /**
     * @param \DateTime $dontDeliverAfter
     * @return Sms
     */
    public function setDontDeliverAfter($dontDeliverAfter)
    {
        $this->dontDeliverAfter = $dontDeliverAfter;

        return $this;
    }
}
