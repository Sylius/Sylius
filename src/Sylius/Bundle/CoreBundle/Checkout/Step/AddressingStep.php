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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AddressingStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();

        if (OrderCheckoutStates::STATE_CART !== $order->getCheckoutState()) {
            $this->applyTransition(OrderCheckoutTransitions::TRANSITION_READDRESS, $order);
        }

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_INITIALIZE, $order);
        $form = $this->createCheckoutAddressingForm($order, $this->getCustomer());

        return $this->renderStep($context, $order, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $context->getRequest();

        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_INITIALIZE, $order);
        $form = $this->createCheckoutAddressingForm($order, $this->getCustomer());

        if ($form->handleRequest($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_PRE_COMPLETE, $order);

            $this->applyTransition(OrderCheckoutTransitions::TRANSITION_ADDRESS, $order);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_COMPLETE, $order);

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
     * @param CustomerInterface $customer
     *
     * @return FormInterface
     */
    protected function createCheckoutAddressingForm(OrderInterface $order, CustomerInterface $customer = null)
    {
        return $this->createForm('sylius_checkout_addressing', $order, ['customer' => $customer]);
    }

    /**
     * @return CustomerInterface|null
     */
    protected function getCustomer()
    {
        return $this->container->get('sylius.context.customer')->getCustomer();
    }
}
