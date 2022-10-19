<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Reactor\AfterPlacedOrder;

use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterOrderCreateProcessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class RequestPaymentReactor implements AfterPlacedOrderReactorInterface
{
    public function __construct(private WorkflowInterface $syliusOrderPaymentWorkflow)
    {
    }

    public function react(OrderInterface $order): void
    {
        $this->syliusOrderPaymentWorkflow->apply($order, OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT);
    }
}
