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
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Sylius\Component\Core\SyliusOrderEvents;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Fernando Caraballo Ortiz <caraballo.ortiz@gmail.com>
 */
class FinalizeStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_INITIALIZE, $order);

        return $this->renderStep($context, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_INITIALIZE, $order);

        $this->completeOrder($order);

        return $this->complete();
    }

    /**
     * @param ProcessContextInterface $context
     * @param OrderInterface $order
     *
     * @return Response
     */
    protected function renderStep(ProcessContextInterface $context, OrderInterface $order)
    {
        return $this->render($this->container->getParameter(sprintf('sylius.checkout.step.%s.template', $this->getName())), [
            'context' => $context,
            'order' => $order,
        ]);
    }

    /**
     * @param OrderInterface $order
     */
    protected function completeOrder(OrderInterface $order)
    {
        $this->get('session')->set('sylius_order_id', $order->getId());

        $currencyProvider = $this->get('sylius.currency_provider');

        $this->dispatchCheckoutEvent(SyliusOrderEvents::PRE_CREATE, $order);
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_PRE_COMPLETE, $order);

        $this->applyTransition(OrderCheckoutTransitions::TRANSITION_COMPLETE, $order);
        $this->get('sm.factory')->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_CREATE, true);
        if ($order->getCurrency() !== $currencyProvider->getBaseCurrency()) {
            $currencyRepository = $this->get('sylius.repository.currency');
            $currency = $currencyRepository->findOneBy(['code' => $order->getCurrency()]);
            $order->setExchangeRate($currency->getExchangeRate());
        }

        $manager = $this->get('sylius.manager.order');
        $manager->persist($order);
        $manager->flush();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_COMPLETE, $order);
        $this->dispatchCheckoutEvent(SyliusOrderEvents::POST_CREATE, $order);

        $cartProvider = $this->getCartProvider();
        $cartProvider->abandonCart();
    }
}
