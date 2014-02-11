<?php

namespace FSi\Bundle\PayumPayuBundle\Payum\Payu;

use Buzz\Client\ClientInterface;
use Buzz\Message\Request;
use Buzz\Message\Response;

class Api
{
    const PAYU_BASE_URL = 'https://secure.payu.com';

    const PAYU_SANDBOX_URL = 'https://sandbox.payu.pl';

    const PAYMENT_STATUS_OK = 'OK';

    const PAYMENT_STATE_NEW = 1;

    const PAYMENT_STATE_CANCELLED = 2;

    const PAYMENT_STATE_REJECTED = 3;

    const PAYMENT_STATE_STARTED = 4;

    const PAYMENT_STATE_PENDING = 5;

    const PAYMENT_STATE_RETURNED = 7;

    const PAYMENT_STATE_COMPLETED = 99;

    protected $options = array(
        'key1' => null,
        'key2' => null,
        'pos_id' => null,
        'pos_auth_key' => null,
        'sandbox' => false,
        'charset' => 'UTF'
    );

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     * @param array $options
     */
    public function __construct(ClientInterface $client, array $options)
    {
        $this->options = array_replace($this->options, $options);
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getNewPaymentUrl()
    {
        return sprintf('%s/paygw/%s/NewPayment', $this->getPayuUrl(), $this->options['charset']);
    }

    /**
     * @param array $details
     * @return array
     */
    public function prepareNewPaymentDetails(array $details)
    {
        $details = array_merge(
            $details,
            array(
                'pos_id' => $this->options['pos_id'],
                'pos_auth_key' => $this->options['pos_auth_key']
            )
        );

        return $details;
    }

    /**
     * @param array $notificationDetails
     * @throws \InvalidArgumentException
     */
    public function validatePaymentNotification(array $notificationDetails)
    {
        if (!array_key_exists('pos_id', $notificationDetails) || !array_key_exists('session_id', $notificationDetails) ||
            !array_key_exists('ts', $notificationDetails) || !array_key_exists('sig', $notificationDetails) ) {
            throw new \InvalidArgumentException(
                "Missing one of pos_id, session_id, ts or sig in payment notification"
            );
        }

        $notificationSignature = md5(
            $notificationDetails['pos_id'] .
            $notificationDetails['session_id'] .
            $notificationDetails['ts'] .
            $this->options['key2']
        );

        if ($notificationSignature != $notificationDetails['sig']) {
            throw new \InvalidArgumentException(
                "Invalid payment notification signature"
            );
        }
    }

    public function getPaymentDetails($notificationDetails)
    {
        $ts = time();
        $paymentSignature = md5(
            $notificationDetails['pos_id'] .
            $notificationDetails['session_id'] .
            $ts .
            $this->options['key1']
        );

        $request = new Request(
            Request::METHOD_POST,
            sprintf('/paygw/%s/Payment/get', $this->options['charset']),
            $this->getPayuUrl()
        );

        $request->setContent(
            sprintf(
                'pos_id=%s&session_id=%s&ts=%s&sig=%s',
                $notificationDetails['pos_id'],
                $notificationDetails['session_id'],
                $ts,
                $paymentSignature
            )
        );

        $response = new Response();
        $this->client->send($request, $response);

        if (!$response->isOk()) {
            throw new \RuntimeException("Can't finish /Payment/get request");
        }

        return $this->parsePaymentDetailsXML($response->getContent());
    }

    public function validatePaymentDetails($paymentDetails)
    {
        $paymentSignature = md5(
            $paymentDetails['pos_id'] .
            $paymentDetails['session_id'] .
            $paymentDetails['order_id'] .
            $paymentDetails['status'] .
            $paymentDetails['amount'] .
            $paymentDetails['desc'] .
            $paymentDetails['ts'] .
            $this->options['key2']
        );

        if ($paymentSignature != $paymentDetails['sig']) {
            throw new \RuntimeException("Invalid payment signature");
        }
    }

    /**
     * @param $xml
     * @throws \RuntimeException
     * @return array
     */
    protected function parsePaymentDetailsXML($xml)
    {
        $paymentDetails = array();
        $xmlData = simplexml_load_string($xml);
        if ((string)$xmlData->status != self::PAYMENT_STATUS_OK) {
            throw new \RuntimeException(
                sprintf(
                    'Invalid payment details status response. Error code: %d',
                    (int)$xmlData->error->nr
                )
            );
        }

        foreach ((array) $xmlData->trans as $key => $value) {
            $paymentDetails[$key] = (string) $value;
        }

        return $paymentDetails;
    }

    protected function getPayuUrl()
    {
        return ($this->options['sandbox'])
            ? self::PAYU_SANDBOX_URL
            : self::PAYU_BASE_URL;
    }
}