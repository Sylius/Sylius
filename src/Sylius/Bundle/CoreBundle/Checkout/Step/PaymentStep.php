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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PaymentStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_INITIALIZE, $order);

        if (OrderCheckoutStates::STATE_SHIPPING_SELECTED !== $order->getCheckoutState()) {
            $this->applyTransition(OrderCheckoutTransitions::TRANSITION_RESELECT_PAYMENT, $order);
        }

        $form = $this->createCheckoutPaymentForm($order);

        return $this->renderStep($context, $order, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $context->getRequest();

        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_INITIALIZE, $order);

        $form = $this->createCheckoutPaymentForm($order);

        if ($form->handleRequest($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_PRE_COMPLETE, $order);

            $this->applyTransition(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT, $order);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::PAYMENT_COMPLETE, $order);

            return $this->complete();
        }

        return $this->renderStep($context, $order, $form);
    }

    /**
     * @param ProcessContextInterface $context
     * @param OrderInterface $order
     * @param FormInterface $form
     *
     * @return Response
     */
    protected function renderStep(ProcessContextInterface $context, OrderInterface $order, FormInterface $form)
    {
        return $this->render($this->container->getParameter(sprintf('sylius.checkout.step.%s.template', $this->getName())), [
            'order' => $order,
            'form' => $form->createView(),
            'context' => $context,
        ]);
    }

    /**
     * @param OrderInterface $order
     *
     * @return FormInterface
     */
    protected function createCheckoutPaymentForm(OrderInterface $order)
    {
        return $this->createForm('sylius_checkout_payment', $order);
    }
}
