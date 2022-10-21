<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\StateResolver;

use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class OrderCheckoutStateResolver implements OrderCheckoutStateResolverInterface
{
    public function __construct(
        private WorkflowInterface $syliusOrderCheckoutWorkflow,
        private OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
        private OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
    ) {
    }

    public function resolve(OrderInterface $order): void
    {
        if (
            !$this->orderShippingMethodSelectionRequirementChecker->isShippingMethodSelectionRequired($order) &&
            $this->syliusOrderCheckoutWorkflow->can($order, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING)
        ) {
            $this->syliusOrderCheckoutWorkflow->apply($order, OrderCheckoutTransitions::TRANSITION_SKIP_SHIPPING);
        }

        if (
            !$this->orderPaymentMethodSelectionRequirementChecker->isPaymentMethodSelectionRequired($order) &&
            $this->syliusOrderCheckoutWorkflow->can($order, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT)
        ) {
            $this->syliusOrderCheckoutWorkflow->apply($order, OrderCheckoutTransitions::TRANSITION_SKIP_PAYMENT);
        }
    }
}
