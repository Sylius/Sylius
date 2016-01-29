<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Action;

use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\Payment as PayumPayment;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Convert;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;

class CapturePaymentAction extends GatewayAwareAction
{
    /**
     * {@inheritdoc}
     *
     * @param $request Capture
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var $payment SyliusPaymentInterface */
        $payment = $request->getModel();
        $order = $payment->getOrder();

        $this->gateway->execute($status = new GetStatus($payment));
        if ($status->isNew()) {
            try {
                $this->gateway->execute($convert = new Convert($payment, 'array', $request->getToken()));
                $payment->setDetails($convert->getResult());
            } catch (RequestNotSupportedException $e) {
                $payumPayment = new PayumPayment();
                $payumPayment->setNumber($order->getNumber());
                $payumPayment->setTotalAmount($order->getTotal());
                $payumPayment->setCurrencyCode($order->getCurrency());
                $payumPayment->setClientEmail($order->getCustomer()->getEmail());
                $payumPayment->setClientId($order->getCustomer()->getId());
                $payumPayment->setDescription(sprintf(
                    'Payment contains %d items for a total of %01.2f',
                    $order->getItems()->count(), $order->getTotal() / 100
                ));
                $payumPayment->setDetails($payment->getDetails());

                $this->gateway->execute($convert = new Convert($payumPayment, 'array', $request->getToken()));
                $payment->setDetails($convert->getResult());
            }
        }

        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        try {
            $request->setModel($details);
            $this->gateway->execute($request);
        } finally {
            $payment->setDetails((array) $details);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }
}
