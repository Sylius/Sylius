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

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Bundle\CoreBundle\Event\PurchaseCompleteEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractPurchaseStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PURCHASE_INITIALIZE, $order);

        return $this->initializePurchase($this->getCurrentCart(), $context);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        $payment = $order->getPayments()->last();

        $this->finalizePurchase($order, $context);

        $nextState = $payment->getState();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PURCHASE_INITIALIZE, $order);

        $stateMachine = $this->get('finite.factory')->get($payment, PaymentTransitions::GRAPH);

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
     * @param OrderInterface $order
     * @param ProcessContextInterface $context
     *
     * @return Response
     */
    abstract protected function initializePurchase(OrderInterface $order, ProcessContextInterface $context);

    /**
     * @param OrderInterface $order
     * @param ProcessContextInterface $context
     */
    abstract protected function finalizePurchase(OrderInterface $order, ProcessContextInterface $context);
}
