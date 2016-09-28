<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\StateResolver;

use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderPaymentTransitions;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class OrderPaymentStateResolver implements StateResolverInterface
{
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(FactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order)
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);
        $completedPaymentTotal = 0;

        $payments = $order->getPayments()->filter(function (PaymentInterface $payment) {
            return PaymentInterface::STATE_COMPLETED === $payment->getState();
        });

        foreach ($payments as $payment) {
            $completedPaymentTotal += $payment->getAmount();
        }

        if (0 < $payments->count() && $completedPaymentTotal >= $order->getTotal()) {
            $this->applyTransition($stateMachine, OrderPaymentTransitions::TRANSITION_PAY);
            return;
        }

        if ($completedPaymentTotal < $order->getTotal() && 0 < $completedPaymentTotal) {
            $this->applyTransition($stateMachine, OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY);
            return;
        }
    }

    /**
     * @param StateMachineInterface $stateMachine
     * @param string $transition
     */
    private function applyTransition(StateMachineInterface $stateMachine, $transition)
    {
        if ($stateMachine->can($transition)) {
            $stateMachine->apply($transition);
        }
    }
}
