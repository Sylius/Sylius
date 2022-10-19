<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\OrderCheckout;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

final class CreateOrderListener
{
    public function __construct(private WorkflowInterface $syliusOrderWorkflow,)
    {
    }

    public function createOrder(Event $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        $this->syliusOrderWorkflow->apply($order, 'create');
    }
}
