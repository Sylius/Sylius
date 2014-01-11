<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Dummy\Action;

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\StatusRequestInterface;
use Sylius\Bundle\CoreBundle\Model\Order;

class OrderStatusAction extends PaymentAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request StatusRequestInterface */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var Order $order */
        $order = $request->getModel();

        $paymentDetails = $order->getPayment()->getDetails();
        if (empty($paymentDetails)) {
            $request->markNew();

            return;
        }

        if (isset($paymentDetails['captured'])) {
            $request->markSuccess();

            return;
        }

        $request->markUnknown();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof StatusRequestInterface &&
            $request->getModel() instanceof Order
        ;
    }
}
