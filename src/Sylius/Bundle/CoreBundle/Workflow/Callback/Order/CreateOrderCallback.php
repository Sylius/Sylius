<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class CreateOrderCallback implements AfterCompletedCheckoutCallbackInterface
{
    public function __construct(private WorkflowInterface $syliusOrderWorkflow,)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->syliusOrderWorkflow->apply($order, OrderTransitions::TRANSITION_CREATE);
    }
}
