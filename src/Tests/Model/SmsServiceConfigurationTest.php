<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PROCERGS\Sms\Tests\Model;

use libphonenumber\PhoneNumber;
use PROCERGS\Sms\Model\Sms;
use PROCERGS\Sms\Model\SmsServiceConfiguration;

class SmsServiceConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testValidConfiguration()
    {
        $config = new SmsServiceConfiguration(
            $sendUri = 'https://example.com/send',
            $receiveUri = 'https://example.com/receive',
            $statusUri = 'https://example.com/status/{id}',
            $realm = 'REALM',
            $systemId = 'SYSTEM_ID',
            $systemKey = 'SYSTEM_KEY',
            $shouldSend = true
        );

        $this->assertEquals($sendUri, $config->getSendUri());
        $this->assertEquals($receiveUri, $config->getReceiveUri());
        $this->assertEquals($statusUri, $config->getStatusUri());
        $this->assertEquals($realm, $config->getRealm());
        $this->assertEquals($systemId, $config->getSystemId());
        $this->assertEquals($systemKey, $config->getSystemKey());
        $this->assertEquals($shouldSend, $config->shouldSend());
    }

    public function testInvalidStatusUri()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new SmsServiceConfiguration(
            $sendUri = 'https://example.com/send',
            $receiveUri = 'https://example.com/receive',
            $statusUri = 'https://example.com/status/',
            $realm = 'REALM',
            $systemId = 'SYSTEM_ID',
            $systemKey = 'SYSTEM_KEY',
            $shouldSend = true
        );
    }
}
