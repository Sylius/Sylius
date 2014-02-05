<?php

namespace FSi\Bundle\PayumPayuBundle\Payum\Payu\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\StatusRequestInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;

class StatusAction implements ActionInterface
{
    /**
     * @inheritdoc
     */
    function execute($request)
    {
        /* @var $request StatusRequestInterface */
        if (false == $this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /* @var $order OrderInterface */
        $order = $request->getModel();
        /* @var $request StatusRequestInterface */
        if ($order->getPaymentState() === PaymentInterface::STATE_NEW) {
            $request->markNew();
            return;
        }


        $request->markUnknown();
    }

    /**
     * @inheritdoc
     */
    function supports($request)
    {
        return $request instanceof StatusRequestInterface &&
            $request->getModel() instanceof OrderInterface;
    }
}