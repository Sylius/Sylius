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

use Sylius\Bundle\CoreBundle\Event\PurchaseCompleteEvent;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Sylius\Component\Payment\Manager\PaymentManagerInterface;
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

        $redirectUrl = $this->getPaymentManager()->initialize(
            $order,
            array(
                'route'    => 'sylius_checkout_forward',
                'stepName' => $this->getName()
            )
        );

        return new RedirectResponse($redirectUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $paymentManager = $this->getPaymentManager();

        $nextState = $paymentManager->handle($this->getRequest());

        /** @var $order OrderInterface */
        $order = $paymentManager->getSubject();

        /** @var $payment PaymentInterface */
        $payment = $order->getPayments()->last();

        if (!$payment instanceof PaymentInterface) {
            throw new UnexpectedTypeException($payment, 'Sylius\Component\Core\Model\PaymentInterface');
        }

        $order = $payment->getOrder();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PURCHASE_INITIALIZE, $order);

        $stateMachine = $this->get('sm.factory')->get($payment, PaymentTransitions::GRAPH);

        if (null !== $transition = $stateMachine->getTransitionToState($nextState)) {
            $stateMachine->apply($transition);
        }

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PURCHASE_PRE_COMPLETE, $order);

        $this->getManager()->flush();

        $event = new PurchaseCompleteEvent($payment);
        $this->dispatchEvent(SyliusCheckoutEvents::PURCHASE_COMPLETE, $event);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        return $this->complete();
    }

    /**
     * @return PaymentManagerInterface
     */
    protected function getPaymentManager()
    {
        return $this->get('sylius.payments.payment_manager');
    }
}
