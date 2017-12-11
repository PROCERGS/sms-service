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
     * Realm for authenticating with the SMS Service
     *
     * @var string
     */
    private $realm;

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
     * This will define the "send" parameter of the request.
     *
     * When false, messages will be sent to the SMS Service but will not be actually sent as SMS.
     * This is useful for testing the API without sending the messages.
     *
     * @var boolean
     */
    private $shouldSend;

    /**
     * SmsServiceConfiguration constructor.
     * @param string $sendUri
     * @param string $receiveUri
     * @param string $statusUri
     * @param string $realm
     * @param string $systemId
     * @param string $systemKey
     * @param int $serviceOrder
     * @param string $from
     * @param $shouldSend
     */
    public function __construct(
        $sendUri,
        $receiveUri,
        $statusUri,
        $realm,
        $systemId,
        $systemKey,
        $serviceOrder,
        $from,
        $shouldSend
    ) {
        if (strstr($statusUri, '{id}') === false) {
            throw new \InvalidArgumentException("Invalid status URI. It's expected for it to have an '{id}' placeholder.");
        }

        $this->sendUri = $sendUri;
        $this->receiveUri = $receiveUri;
        $this->statusUri = $statusUri;
        $this->realm = $realm;
        $this->systemId = $systemId;
        $this->systemKey = $systemKey;
        $this->serviceOrder = $serviceOrder;
        $this->from = $from;
        $this->shouldSend = $shouldSend;
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
    public function getRealm()
    {
        return $this->realm;
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

    /**
     * @return bool
     */
    public function shouldSend()
    {
        return $this->shouldSend;
    }
}
