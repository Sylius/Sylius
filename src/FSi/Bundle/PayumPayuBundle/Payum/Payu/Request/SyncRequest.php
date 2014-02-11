<?php

namespace FSi\Bundle\PayumPayuBundle\Payum\Payu\Request;

use Payum\Core\Request\SyncRequest as BaseRequest;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;

class SyncRequest extends BaseRequest
{
    /**
     * @var array
     */
    private $paymentDetails;

    /**
     * @param OrderInterface $model
     * @param array $paymentDetails
     */
    public function __construct(OrderInterface $model, array $paymentDetails)
    {
        parent::__construct($model);
        $this->paymentDetails = $paymentDetails;
    }

    /**
     * @return array
     */
    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }
}