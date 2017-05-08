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
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Order\StateResolver\TargetTransitionResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderPaymentStateResolver implements StateResolverInterface
{
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var TargetTransitionResolverInterface
     */
    private $transitionResolver;

    /**
     * @param FactoryInterface $stateMachineFactory
     * @param TargetTransitionResolverInterface $transitionResolver
     */
    public function __construct(FactoryInterface $stateMachineFactory, TargetTransitionResolverInterface $transitionResolver)
    {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->transitionResolver = $transitionResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order)
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);
        $targetTransition = $this->transitionResolver->resolve($order);

        if (null !== $targetTransition) {
            $this->applyTransition($stateMachine, $targetTransition);
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
