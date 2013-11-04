<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Checkout\Step;

use Payum\Bundle\PayumBundle\Security\TokenFactory;
use Payum\Registry\RegistryInterface;
use Payum\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\CoreBundle\Checkout\Step\CheckoutStep;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PurchaseStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();

        $captureToken = $this->getTokenFactory()->createCaptureToken(
            $order->getPayment()->getMethod()->getGateway(),
            $order,
            'sylius_checkout_forward',
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
        $this->getCartProvider()->abandonCart();

        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        $status = new StatusRequest($token);
        $payment->execute($status);

        /** @var OrderInterface $order */
        $order = $status->getModel();

        if (!$order instanceof OrderInterface) {
            throw new \RuntimeException(sprintf('Expected order to be set as model but it is %s', get_class($order)));
        }

        $order->getPayment()->setState($status->getStatus());

        if ($status->isSuccess()) {
            $type = 'success';
            $msg  = 'sylius.checkout.success';
        } elseif ($status->isPending()) {
            $type = 'notice';
            $msg  = 'sylius.checkout.pending';
        } elseif ($status->isCanceled()) {
            $type = 'notice';
            $msg  = 'sylius.checkout.canceled';
        } elseif ($status->isExpired()) {
            $type = 'notice';
            $msg  = 'sylius.checkout.expired';
        } elseif ($status->isSuspended()) {
            $type = 'notice';
            $msg  = 'sylius.checkout.suspended';
        } elseif ($status->isFailed()) {
            $type = 'error';
            $msg  = 'sylius.checkout.failed';
        } else {
            $type = 'error';
            $msg  = 'sylius.checkout.unknown';
        }

        //TODO: an event here

        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add($type, $this->get('translator')->trans($msg, array(), 'flashes'));

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
