<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class CancelOrderPaymentCallback implements AfterCanceledOrderCallbackInterface
{
    public function __construct(private WorkflowInterface $syliusOrderPaymentWorkflow)
    {
    }

    public function call(OrderInterface $order): void
    {
        if ($this->syliusOrderPaymentWorkflow->can($order, OrderPaymentTransitions::TRANSITION_CANCEL)) {
            $this->syliusOrderPaymentWorkflow->apply($order, OrderPaymentTransitions::TRANSITION_CANCEL);
        }
    }
}
