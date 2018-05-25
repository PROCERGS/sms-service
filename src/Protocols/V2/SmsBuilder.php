<?php

namespace PROCERGS\Sms\Protocols\V2;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use PROCERGS\Sms\Exception\InvalidPhoneNumberException;
use PROCERGS\Sms\Exception\InvalidStatusException;
use PROCERGS\Sms\Model\TimeConstraint;
use PROCERGS\Sms\Model\TimeConstraintInterface;

final class SmsBuilder
{
    /** @var string */
    private $to;

    /** @var string */
    private $text;

    /** @var TimeConstraintInterface */
    private $deliveryTimeConstraint;

    /** @var bool */
    private $shouldSend;

    /** @var int */
    private $id;

    /** @var \DateTime */
    private $sendDate;

    /** @var \DateTime */
    private $deliveryDate;

    /** @var string */
    private $statusCode;

    /** @var string */
    private $statusDetails;

    /**
     * SmsBuilder constructor.
     * @param string $to
     * @param string $text
     */
    public function __construct($to, $text)
    {
        $this->setTo($to);
        $this->setText($text);
        $this->setDeliveryTimeConstraint(new TimeConstraint());
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string|PhoneNumber $to
     * @return SmsBuilder
     */
    private function setTo($to)
    {
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $phone = $to instanceof PhoneNumber ? $to : $phoneUtil->parse($to, 'BR');
            $allowedTypes = [PhoneNumberType::MOBILE, PhoneNumberType::FIXED_LINE_OR_MOBILE];
            if (false === array_search($phoneUtil->getNumberType($phone), $allowedTypes)) {
                throw new InvalidPhoneNumberException("The given phone does not seem to be a mobile number");
            }

            $this->to = $phone;

            return $this;
        } catch (NumberParseException $e) {
            throw new InvalidPhoneNumberException($e->getMessage());
        }
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return SmsBuilder
     */
    private function setText($text)
    {
        $this->text = $text;

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
     * @return SmsBuilder
     */
    public function setDeliveryTimeConstraint($deliveryTimeConstraint)
    {
        $this->deliveryTimeConstraint = $deliveryTimeConstraint;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShouldSend()
    {
        return $this->shouldSend;
    }

    /**
     * @param bool $shouldSend
     * @return SmsBuilder
     */
    public function setShouldSend($shouldSend)
    {
        $this->shouldSend = $shouldSend;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return SmsBuilder
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $sendDate
     * @return SmsBuilder
     */
    public function setSendDate($sendDate)
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    /**
     * @param string $deliveryDate
     * @return SmsBuilder
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * @param string $status
     * @return SmsBuilder
     */
    public function setStatus($status)
    {
        $this->validateStatus($status);
        $this->statusCode = $status;

        return $this;
    }

    /**
     * @param string $statusDetails
     * @return SmsBuilder
     */
    public function setStatusDetails($statusDetails)
    {
        $this->statusDetails = $statusDetails;

        return $this;
    }

    /**
     * @return Sms
     */
    public function build()
    {
        return new Sms($this);
    }

    private function validateStatus($status)
    {
        $validStatuses = [
            'WAITING_TO_BE_SENT',
            'ERROR_IN_SEND',
            'WAITING_TO_BE_DELIVERED',
            'DELIVERED',
            'ERROR_IN_DELIVERY',
            'NO_DELIVERY_CONFIRMATION',
            'NOT_SEND',
        ];

        if (array_search($status, $validStatuses) === false) {
            throw new InvalidStatusException("'{$status}' does not seem to be a supported status.");
        }
    }

    /**
     * @return SmsStatus
     */
    public function getStatus()
    {
        return new SmsStatus($this->sendDate, $this->deliveryDate, $this->statusCode, $this->statusDetails);
    }
}
