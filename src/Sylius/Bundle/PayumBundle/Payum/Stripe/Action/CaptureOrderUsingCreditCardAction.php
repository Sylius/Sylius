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

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\SecuredCaptureRequest;
use Payum\Core\Security\SensitiveValue;
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
                'card' => new SensitiveValue(array(
                    'number' => $obtainCreditCardRequest->getCreditCard()->getNumber(),
                    'expiryMonth' => $obtainCreditCardRequest->getCreditCard()->getExpiryMonth(),
                    'expiryYear' => $obtainCreditCardRequest->getCreditCard()->getExpiryYear(),
                    'cvv' => $obtainCreditCardRequest->getCreditCard()->getSecurityCode()
                )),
                'amount' => number_format($order->getTotal() / 100, 2),
                'currency' => $order->getCurrency(),
            );

            $payment->setDetails($details);
        }

        try {
            $request->setModel($payment);
            $this->payment->execute($request);
            $request->setModel($order);
        } catch (\Exception $e) {
            $request->setModel($order);

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
