<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

final class RequestPaymentListener
{
    public function __construct(private WorkflowInterface $syliusOrderPaymentWorkflow)
    {
    }

    public function requestPayment(Event $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        $this->syliusOrderPaymentWorkflow->apply($order, OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT);
    }
}
