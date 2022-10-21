<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class CancelShipmentCallback implements AfterCanceledOrderCallbackInterface
{
    public function __construct(private WorkflowInterface $syliusShipmentWorkflow)
    {
    }

    public function call(OrderInterface $order): void
    {
        foreach ($order->getShipments() as $payment) {
            if (!$this->syliusShipmentWorkflow->can($payment, ShipmentTransitions::TRANSITION_CANCEL)) {
                continue;
            }

            $this->syliusShipmentWorkflow->apply($payment, ShipmentTransitions::TRANSITION_CANCEL);
        }
    }
}
