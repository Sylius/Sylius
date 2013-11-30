<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Stripe\Action;

use Payum\Action\PaymentAwareAction;
use Payum\Bridge\Spl\ArrayObject;
use Payum\Exception\RequestNotSupportedException;
use Payum\Request\CaptureRequest;
use Payum\Request\SecuredCaptureRequest;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PayumBundle\Payum\Request\ObtainCreditCardRequest;

class CaptureOrderUsingCreditCardAction extends PaymentAwareAction
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
        $payment = $order->getPayment();

        $details = $payment->getDetails();
        if (empty($details)) {
            $this->payment->execute($obtainCreditCardRequest = new ObtainCreditCardRequest($order));

            $details = array(
                'card' => array(
                    'number' => $obtainCreditCardRequest->getCreditCard()->getNumber(),
                    'expiryMonth' => $obtainCreditCardRequest->getCreditCard()->getExpiryMonth(),
                    'expiryYear' => $obtainCreditCardRequest->getCreditCard()->getExpiryYear(),
                    'cvv' => $obtainCreditCardRequest->getCreditCard()->getSecurityCode()
                ),
                'amount' => number_format($order->getTotal() / 100, 2),
                'currency' => $order->getCurrency(),
            );

            $payment->setDetails($details);
        }

        try {
            $request->setModel($payment);
            $this->payment->execute($request);

            $request->setModel($order);

            //TODO: when sensitive value object is used this would be removed. Require update to payum 0.7.
            $details = $payment->getDetails();
            unset($details['card']);
            $payment->setDetails($details);
        } catch (\Exception $e) {
            //TODO: when sensitive value object is used this would be removed. Require update to payum 0.7.
            $details = $payment->getDetails();
            unset($details['card']);
            $payment->setDetails($details);

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
