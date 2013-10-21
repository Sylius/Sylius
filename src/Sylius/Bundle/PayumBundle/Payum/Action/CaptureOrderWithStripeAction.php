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

use Payum\Action\PaymentAwareAction;
use Payum\Bridge\Spl\ArrayObject;
use Payum\Exception\RequestNotSupportedException;
use Payum\Request\CaptureRequest;
use Payum\Request\SecuredCaptureRequest;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PayumBundle\Payum\Request\ObtainCreditCardRequest;

class CaptureOrderWithStripeAction extends PaymentAwareAction
{
    /**
     * {@inheritdoc}
     */
    public function execute($request)
    {
        /** @var $request SecuredCaptureRequest */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var OrderInterface $order */
        $order = $request->getModel();

        $paymentDetails = $order->getPayment()->getDetails();
        if (empty($paymentDetails)) {
            $this->payment->execute($obtainCreditCardRequest = new ObtainCreditCardRequest($order));

            $paymentDetails = array(
                'card' => array(
                    'number' => $obtainCreditCardRequest->getCreditCard()->getNumber(),
                    'expiryMonth' => $obtainCreditCardRequest->getCreditCard()->getExpiryMonth(),
                    'expiryYear' => $obtainCreditCardRequest->getCreditCard()->getExpiryYear(),
                    'cvv' => $obtainCreditCardRequest->getCreditCard()->getSecurityCode()
                ),
                'amount' => number_format($order->getTotal() / 100, 2),
                'currency' => $order->getCurrency(),
            );
        }

        // TODO: find a way to simply the next logic

        $paymentDetails = ArrayObject::ensureArrayObject($paymentDetails);

        try {
            $this->payment->execute(new CaptureRequest($paymentDetails));

            unset($paymentDetails['card']);
            $order->getPayment()->setDetails((array) $paymentDetails);
        } catch (\Exception $e) {
            unset($paymentDetails['card']);
            $order->getPayment()->setDetails((array) $paymentDetails);

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SecuredCaptureRequest &&
            $request->getModel() instanceof OrderInterface
        ;
    }
}
