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

use PROCERGS\Sms\Protocols\AbstractPayload;
use PROCERGS\Sms\Protocols\SmsInterface;

final class InputSms extends AbstractPayload
{
    /** @var array */
    private $payload;

    /**
     * SmsV2 constructor.
     * @param SmsInterface $sms
     */
    public function __construct(SmsInterface $sms)
    {
        $constraint = $sms->getDeliveryTimeConstraint();
        $beginTime = $constraint->getStartTime();
        $endTime = $constraint->getEndTime();
        $payload = [
            'to' => $sms->getTo()->getNationalNumber(),
            'text' => $sms->getMessage(),
            'send' => $sms->isShouldSend(),
            'beginTime' => !is_null($beginTime) ? $beginTime->__toString() : null,
            'endTime' => !is_null($endTime) ? $endTime->__toString() : null,
        ];

        $this->payload = array_filter($payload, function ($value) {
            return $value !== null;
        });
    }

    public function getPayload()
    {
        return $this->payload;
    }
}
