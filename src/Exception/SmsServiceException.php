<?php

namespace PROCERGS\Sms\Exception;


class SmsServiceException extends \Exception
{
    /** @var array */
    protected $errorResponse;

    public function __construct($errorResponse, $code = null)
    {
        $this->code = $code;
        $this->errorResponse = $errorResponse;
        if (is_array($errorResponse) && count($errorResponse) === 1) {
            $error = reset($errorResponse);
            if (is_array($error)) {
                $message = array_key_exists('message', $error) ? $error['message'] : 'Unknown error';
                $details = array_key_exists('detail', $error) ? $error['detail'] : 'No details informed';
            } else {
                $message = property_exists($error, 'message') ? $error->message : 'Unknown error';
                $details = property_exists($error, 'detail') ? $error->detail : 'No details informed';
            }
            $this->message = sprintf("%s: %s", $message, $details);
        } else {
            $this->message = $errorResponse;
        }
    }

    /**
     * @return array
     */
    public function getErrorResponse()
    {
        return $this->errorResponse;
    }

    /**
     * @param array $errorResponse
     * @return SmsServiceException
     */
    public function setErrorResponse($errorResponse)
    {
        $this->errorResponse = $errorResponse;

        return $this;
    }
}
