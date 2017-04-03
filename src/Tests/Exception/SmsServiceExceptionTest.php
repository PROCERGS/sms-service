<?php

namespace PROCERGS\Sms\Tests\Exception;

use PROCERGS\Sms\Exception\SmsServiceException;

class SmsServiceExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testSmsServiceException()
    {
        $response = 'error happened';
        $response2 = 'other response';
        $code = 500;

        $exception = new SmsServiceException($response, $code);

        $this->assertEquals($response, $exception->getErrorResponse());
        $exception->setErrorResponse($response2);
        $this->assertEquals($response2, $exception->getErrorResponse());
    }
}
