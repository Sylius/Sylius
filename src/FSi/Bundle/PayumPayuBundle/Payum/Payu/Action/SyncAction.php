<?php

namespace FSi\Bundle\PayumPayuBundle\Payum\Payu\Action;

use FSi\Bundle\PayumPayuBundle\Payum\Payu\Api;
use FSi\Bundle\PayumPayuBundle\Payum\Payu\Request\SyncRequest;
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;

class SyncAction extends PaymentAwareAction
{
    function execute($request)
    {
        /* @var $request \FSi\Bundle\PayumPayuBundle\Payum\Payu\Request\SyncRequest */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        $details = $request->getPaymentDetails();

        if (!isset($details['status'])) {
            return ;
        }

        /* @var $order \Sylius\Bundle\CoreBundle\Model\OrderInterface */
        $order = $request->getModel();

        if ($details['status'] === Api::PAYMENT_STATE_NEW) {
            $order->getPayment()->setState(PaymentInterface::STATE_NEW);
            return;
        }

        if ($details['status'] === Api::PAYMENT_STATE_COMPLETED) {
            $order->getPayment()->setState(PaymentInterface::STATE_COMPLETED);
            return;
        }

        if ($details['status'] === Api::PAYMENT_STATE_CANCELLED) {
            $order->getPayment()->setState(PaymentInterface::STATE_CANCELLED);
            return;
        }

        if ($details['status'] === Api::PAYMENT_STATE_PENDING) {
            $order->getPayment()->setState(PaymentInterface::STATE_PENDING);
            return;
        }

    }

    function supports($request)
    {
        return $request instanceof SyncRequest
            && $request->getModel() instanceof OrderInterface;
    }
}