<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\StateResolver;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CheckoutStateResolver implements StateResolverInterface
{
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var OrderPaymentMethodSelectionRequirementCheckerInterface
     */
    private $orderPaymentMethodSelectionRequirementChecker;

    /**
     * @var OrderShippingMethodSelectionRequirementCheckerInterface
     */
    private $orderShippingMethodSelectionRequirementChecker;

    /**
     * @param FactoryInterface $stateMachineFactory
     * @param OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker
     * @param OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
     */
    public function __construct(
        FactoryInterface $stateMachineFactory,
        OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderPaymentMethodSelectionRequirementChecker = $orderPaymentMethodSelectionRequirementChecker;
        $this->orderShippingMethodSelectionRequirementChecker = $orderShippingMethodSelectionRequirementChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(OrderInterface $order)
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        if (
            !$this->orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order)
            && $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
        ) {
            $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING);
        }

        if (
            !$this->orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order)
            && $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
        ) {
            $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT);
        }
    }
}
