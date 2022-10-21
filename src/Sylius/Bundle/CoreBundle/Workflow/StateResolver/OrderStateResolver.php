<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\StateResolver;

use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class OrderStateResolver implements OrderStateResolverInterface
{
    public function __construct(private WorkflowInterface $syliusOrderWorkflow)
    {
    }

    public function resolve(OrderInterface $order): void
    {
        if (
            $this->canOrderBeFulfilled($order) &&
            $this->syliusOrderWorkflow->can($order, OrderTransitions::TRANSITION_FULFILL)
        ) {
            $this->syliusOrderWorkflow->apply($order, OrderTransitions::TRANSITION_FULFILL);
        }
    }

    private function canOrderBeFulfilled(OrderInterface $order): bool
    {
        return
            OrderPaymentStates::STATE_PAID === $order->getPaymentState() &&
            OrderShippingStates::STATE_SHIPPED === $order->getShippingState()
        ;
    }
}
