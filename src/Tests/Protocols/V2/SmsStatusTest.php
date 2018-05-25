<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\Sms\Tests\Protocols\V2;

use PROCERGS\Sms\Protocols\SmsStatusInterface;
use PROCERGS\Sms\Protocols\V2\SmsStatus;

class SmsStatusTest extends \PHPUnit_Framework_TestCase
{
    public function testSmsStatus()
    {
        $status = new SmsStatus(
            $dateSent = new \DateTime(),
            $dateDelivered = new \DateTime(),
            $statusCode = SmsStatusInterface::DELIVERED,
            $statusDesc = 'Some Description'
        );

        $this->assertSame($dateSent, $status->getDateSent());
        $this->assertSame($dateDelivered, $status->getDateDelivered());
        $this->assertSame(SmsStatusInterface::DELIVERED, $status->getStatusCode());
        $this->assertSame($statusDesc, $status->getStatusDetails());
        $this->assertTrue($status->isFinal());
    }
}
