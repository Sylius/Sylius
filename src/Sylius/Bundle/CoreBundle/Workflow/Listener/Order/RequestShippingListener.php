<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderShippingTransitions;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

final class RequestShippingListener
{
    public function __construct(private WorkflowInterface $syliusOrderShippingWorkflow,)
    {
    }

    public function requestShipping(Event $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        $this->syliusOrderShippingWorkflow->apply($order, OrderShippingTransitions::TRANSITION_REQUEST_SHIPPING);
    }
}
