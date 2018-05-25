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

use PROCERGS\Sms\Protocols\SmsStatusInterface;

final class SmsStatus implements SmsStatusInterface
{
    /** @var \DateTime */
    private $dateSent;

    /** @var \DateTime */
    private $dateDelivered;

    /** @var string */
    private $statusCode;

    /** @var string */
    private $statusDetails;

    public function __construct(
        \DateTime $dateSent = null,
        \DateTime $dateDelivered = null,
        $statusCode = null,
        $statusDetails = null
    ) {
        $this->dateSent = $dateSent;
        $this->dateDelivered = $dateDelivered;
        $this->statusCode = $statusCode;
        $this->statusDetails = $statusDetails;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateSent()
    {
        return $this->dateSent;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateDelivered()
    {
        return $this->dateDelivered;
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string|null
     */
    public function getStatusDetails()
    {
        return $this->statusDetails;
    }

    /**
     * @return bool true if the current status is final
     */
    public function isFinal()
    {
        return array_search($this->getStatusCode(), [
                self::DELIVERED,
                self::ERROR_IN_DELIVERY,
                self::ERROR_IN_SEND,
                self::NOT_SEND,
                self::NO_DELIVERY_CONFIRMATION,
            ]) !== false;
    }
}
