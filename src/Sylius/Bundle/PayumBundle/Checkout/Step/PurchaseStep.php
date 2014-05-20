<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumCoreBundle\Checkout\Step;

use Payum\Bundle\PayumBundle\Security\TokenFactory;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\CoreBundle\Checkout\Step\AbstractPurchaseStep;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PurchaseStep extends AbstractPurchaseStep
{
    /**
     * {@inheritdoc}
     */
    protected function initializePurchase(OrderInterface $order)
    {
        $payment = $order->getPayments()->last();

        $captureToken = $this->getTokenFactory()->createCaptureToken(
            $payment->getMethod()->getGateway(),
            $payment,
            'sylius_checkout_forward',
            array('stepName' => $this->getName())
        );

        return new RedirectResponse($captureToken->getTargetUrl());
    }

    /**
     * @param OrderInterface $order
     *
     * @throws \RuntimeException if orders mismatched
     */
    protected function finalizePurchase(OrderInterface $order)
    {
        $token = $this->getHttpRequestVerifier()->verify($this->getRequest());
        $this->getHttpRequestVerifier()->invalidate($token);

        $status = new StatusRequest($token);
        $this->getPayum()->getPayment($token->getPaymentName())->execute($status);

        $payment = $status->getModel();
        if (!$payment instanceof PaymentInterface) {
            throw new UnexpectedTypeException($payment, 'Sylius\Component\Core\Model\PaymentInterface');
        }
        if ($order !== $payment->getOrder()) {
            throw new \LogicException('Current order does not match one associated with the payment.');
        }

        $payment->setState($status->getStatus());
    }

    /**
     * @return RegistryInterface
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @return TokenFactory
     */
    protected function getTokenFactory()
    {
        return $this->get('payum.security.token_factory');
    }

    /**
     * @return HttpRequestVerifierInterface
     */
    protected function getHttpRequestVerifier()
    {
        return $this->get('payum.security.http_request_verifier');
    }
}
