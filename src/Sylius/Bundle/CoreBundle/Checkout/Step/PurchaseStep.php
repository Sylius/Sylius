<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Checkout\Step;

use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\CoreBundle\Event\PurchaseCompleteEvent;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\PayumBundle\Payum\Request\GetStatus;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PurchaseStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PURCHASE_INITIALIZE, $order);

        /** @var $payment PaymentInterface */
        $payment = $order->getPayments()->last();

        $captureToken = $this->getTokenFactory()->createCaptureToken(
            $payment->getMethod()->getGateway(),
            $payment,
            $context->getProcess()->getForwardRoute(),
            array('stepName' => $this->getName())
        );

        return new RedirectResponse($captureToken->getTargetUrl());
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $token = $this->getHttpRequestVerifier()->verify($this->getRequest());
        $this->getHttpRequestVerifier()->invalidate($token);

        $status = new GetStatus($token);
        $this->getPayum()->getPayment($token->getPaymentName())->execute($status);

        /** @var $payment PaymentInterface */
        $payment = $status->getFirstModel();
        $order = $payment->getOrder();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PURCHASE_INITIALIZE, $order);

        $nextState = $status->getValue();

        $stateMachine = $this->get('sm.factory')->get($payment, PaymentTransitions::GRAPH);

        if (null !== $transition = $stateMachine->getTransitionToState($nextState)) {
            $stateMachine->apply($transition);
        }

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PURCHASE_PRE_COMPLETE, $order);

        $this->getDoctrine()->getManager()->flush();

        $event = new PurchaseCompleteEvent($payment);
        $this->dispatchEvent(SyliusCheckoutEvents::PURCHASE_COMPLETE, $event);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        return $this->complete();
    }

    /**
     * @return RegistryInterface
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @return GenericTokenFactoryInterface
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
