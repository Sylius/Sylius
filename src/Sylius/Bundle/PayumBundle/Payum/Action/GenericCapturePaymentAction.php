<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Action;

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\Order;
use Payum\Core\Request\Capture;
use Sylius\Component\Payment\Model\PaymentInterface;

class GenericCapturePaymentAction extends PaymentAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @param $request Capture
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var $payment PaymentInterface */
        $payment = $request->getModel();
        $order = $payment->getOrder();

        $payumOrder = new Order();
        $payumOrder->setNumber($order->getNumber());
        $payumOrder->setTotalAmount($order->getTotal());
        $payumOrder->setCurrencyCode($order->getCurrency());
        $payumOrder->setClientEmail($order->getEmail());
        $payumOrder->setClientId($order->getUser() ? $order->getUser()->getId() : $order->getEmail());
        $payumOrder->setDescription(sprintf(
            'Order containing %d items for a total of %01.2f',
            $order->getItems()->count(), $order->getTotal() / 100
        ));
        $payumOrder->setDetails($payment->getDetails());

        try {
            $request->setModel($payumOrder);
            $this->payment->execute($request);

            $payment->setDetails($payumOrder->getDetails());
            $request->setModel($payment);
        } catch (\Exception $e) {
            $payment->setDetails($payumOrder->getDetails());
            $request->setModel($payment);

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}
