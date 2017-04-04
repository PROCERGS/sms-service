<?php

namespace PROCERGS\Sms\Model;


class SmsServiceConfiguration
{
    /**
     * URI used to send SMS messages
     *
     * @var string
     */
    private $sendUri;

    /**
     * URI used to fetch SMS messages
     *
     * @var string
     */
    private $receiveUri;

    /**
     * URI used to get status of sent SMS messages
     *
     * @var string
     */
    private $statusUri;

    /**
     * System Id for authenticating with the SMS Service
     *
     * @var string
     */
    private $systemId;

    /**
     * System Key for authenticating with the SMS Service
     *
     * @var string
     */
    private $systemKey;

    /**
     * Additional authentication information.
     *
     * @var int
     */
    private $serviceOrder;

    /**
     * String that'll be prepended to the message resulting in a message in the following format:
     * FROM_STRING: MESSAGE
     *
     * @var string
     */
    private $from;

    /**
     * SmsServiceConfiguration constructor.
     * @param string $sendUri
     * @param string $receiveUri
     * @param string $statusUri
     * @param string $systemId
     * @param string $systemKey
     * @param int $serviceOrder
     * @param string $from
     */
    public function __construct($sendUri, $receiveUri, $statusUri, $systemId, $systemKey, $serviceOrder, $from)
    {
        $this->sendUri = $sendUri;
        $this->receiveUri = $receiveUri;
        $this->statusUri = $statusUri;
        $this->systemId = $systemId;
        $this->systemKey = $systemKey;
        $this->serviceOrder = $serviceOrder;
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getSendUri()
    {
        return $this->sendUri;
    }

    /**
     * @return string
     */
    public function getReceiveUri()
    {
        return $this->receiveUri;
    }

    /**
     * @return string
     */
    public function getStatusUri()
    {
        return $this->statusUri;
    }

    /**
     * @return string
     */
    public function getSystemId()
    {
        return $this->systemId;
    }

    /**
     * @return string
     */
    public function getSystemKey()
    {
        return $this->systemKey;
    }

    /**
     * @return int
     */
    public function getServiceOrder()
    {
        return $this->serviceOrder;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }
}
