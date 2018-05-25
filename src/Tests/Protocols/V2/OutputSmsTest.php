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

use PROCERGS\Sms\Protocols\SmsInterface;
use PROCERGS\Sms\Protocols\V2\OutputSms;

class OutputSmsTest extends \PHPUnit_Framework_TestCase
{
    public function testOutput()
    {
        $rawPayload = '{"id":123456,"to":"51999999999","text":"my message","beginTime":"09:00","endTime":null,"send":true,"sendDate":"2018-05-23T10:02:20.139-03:00","deliveryDate":null,"status":"WAITING_TO_BE_DELIVERED","statusDetails":"Waiting delivery"}';

        $payload = [
            'to' => '51999999999',
            'text' => 'my message',
            'beginTime' => '09:00',
            'endTime' => null,
            'send' => true,
            'id' => 123456,
            'sendDate' => '2017-09-13T13:00:36.000-02:00',
            'deliveryDate' => null,
            'status' => 'WAITING_TO_BE_DELIVERED',
            'statusDetails' => 'Waiting delivery',
        ];
        $payload = json_decode($rawPayload, true);

        /** @var SmsInterface $output */
        $output = (new OutputSms())->receive($payload);

        $this->assertSame('51999999999', $output->getTo()->getNationalNumber());
        $this->assertSame('my message', $output->getText());
        $this->assertSame('09:00', (string)$output->getDeliveryTimeConstraint()->getStartTime());
        $this->assertNull($output->getEndTime());
        $this->assertTrue($output->isShouldSend());
        $this->assertSame(123456, $output->getId());
        $this->assertInstanceOf('\DateTime', $output->getStatus()->getDateSent());
        $this->assertNull($output->getStatus()->getDateDelivered());
        $this->assertSame('WAITING_TO_BE_DELIVERED', $output->getStatus()->getStatusCode());
        $this->assertSame('Waiting delivery', $output->getStatus()->getStatusDetails());
    }

    public function testInvalidStatusDate()
    {
        $this->setExpectedException('PROCERGS\Sms\Exception\InvalidTimeException');
        $rawPayload = '{"id":123456,"to":"51999999999","text":"my message","beginTime":"09:00","endTime":null,"send":true,"sendDate":"2018-05-23","deliveryDate":null,"status":"WAITING_TO_BE_DELIVERED","statusDetails":"Waiting delivery"}';

        $payload = json_decode($rawPayload, true);
        (new OutputSms())->receive($payload);
    }
}
