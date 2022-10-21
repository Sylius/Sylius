<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\AfterPlacedOrder;

use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterOrderCreateProcessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class CreatePaymentCallback implements AfterPlacedOrderCallbackInterface
{
    public function __construct(private WorkflowInterface $syliusPaymentWorkflow)
    {
    }

    public function call(OrderInterface $order): void
    {
        foreach ($order->getPayments() as $payment) {
            $this->syliusPaymentWorkflow->apply($payment, PaymentTransitions::TRANSITION_CREATE);
        }
    }
}
