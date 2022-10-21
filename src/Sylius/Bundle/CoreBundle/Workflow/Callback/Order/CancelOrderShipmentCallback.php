<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\OrderShippingTransitions;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class CancelOrderShipmentCallback implements AfterCanceledOrderCallbackInterface
{
    public function __construct(private WorkflowInterface $syliusOrderShippingWorkflow)
    {
    }

    public function call(OrderInterface $order): void
    {
        if ($this->syliusOrderShippingWorkflow->can($order, OrderShippingTransitions::TRANSITION_CANCEL)) {
            $this->syliusOrderShippingWorkflow->apply($order, OrderShippingTransitions::TRANSITION_CANCEL);
        }
    }
}
