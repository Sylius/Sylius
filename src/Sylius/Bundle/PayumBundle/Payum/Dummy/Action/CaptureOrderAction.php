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
use Payum\Core\Request\CaptureRequest;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentAwareInterface;

class CaptureOrderAction extends PaymentAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request CaptureRequest */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var OrderInterface|PaymentAwareInterface $order */
        $order = $request->getModel();
        $payment = $order->getPayment();
        $payment->setAmount($order->getTotal());

        $paymentDetails = $payment->getDetails();
        if (empty($paymentDetails)) {
            $payment->setDetails(array(
                'description' => 'The payment is done by dummy payum extension.',
                'captured' => true,
            ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof CaptureRequest &&
            $request->getModel() instanceof OrderInterface &&
            $request->getModel() instanceof PaymentAwareInterface
        ;
    }
}
