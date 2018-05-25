<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\Sms\Protocols;

use libphonenumber\PhoneNumber;
use PROCERGS\Sms\Model\TimeConstraintInterface;

interface SmsInterface
{
    /**
     * @param PhoneNumber $to
     * @param string $message
     * @param bool $shouldSend
     * @return SmsInterface
     */
    public static function createSimpleSms(PhoneNumber $to, $message, $shouldSend = true);

    /**
     * @return PhoneNumber
     */
    public function getTo();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return bool
     */
    public function isShouldSend();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return TimeConstraintInterface
     */
    public function getDeliveryTimeConstraint();

    /**
     * @return SmsStatusInterface|null
     */
    public function getStatus();
}
