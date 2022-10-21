<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class CancelPaymentCallback implements AfterCanceledOrderCallbackInterface
{
    public function __construct(private WorkflowInterface $syliusPaymentWorkflow)
    {
    }

    public function call(OrderInterface $order): void
    {
        foreach ($order->getPayments() as $payment) {
            if (!$this->syliusPaymentWorkflow->can($payment, PaymentTransitions::TRANSITION_CANCEL)) {
                continue;
            }

            $this->syliusPaymentWorkflow->apply($payment, PaymentTransitions::TRANSITION_CANCEL);
        }
    }
}
