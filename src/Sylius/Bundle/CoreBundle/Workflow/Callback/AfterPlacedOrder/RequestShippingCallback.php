<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\AfterPlacedOrder;

use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterOrderCreateProcessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderShippingTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class RequestShippingCallback implements AfterPlacedOrderCallbackInterface
{
    public function __construct(private WorkflowInterface $syliusOrderShippingWorkflow,)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->syliusOrderShippingWorkflow->apply($order, OrderShippingTransitions::TRANSITION_REQUEST_SHIPPING);
    }
}
