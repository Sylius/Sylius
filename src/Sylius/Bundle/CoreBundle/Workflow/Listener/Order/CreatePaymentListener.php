<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

final class CreatePaymentListener
{
    public function __construct(private WorkflowInterface $syliusPaymentWorkflow)
    {
    }

    public function createPayment(Event $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        foreach ($order->getPayments() as $payment) {
            $this->syliusPaymentWorkflow->apply($payment, PaymentTransitions::TRANSITION_CREATE);
        }
    }
}
