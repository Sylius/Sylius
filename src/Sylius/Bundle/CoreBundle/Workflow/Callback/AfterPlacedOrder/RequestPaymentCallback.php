<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\AfterPlacedOrder;

use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterOrderCreateProcessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class RequestPaymentCallback implements AfterPlacedOrderCallbackInterface
{
    public function __construct(private WorkflowInterface $syliusOrderPaymentWorkflow)
    {
    }

    public function call(OrderInterface $order): void
    {
        if ($this->syliusOrderPaymentWorkflow->can($order, OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT)) {
            $this->syliusOrderPaymentWorkflow->apply($order, OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT);
        }
    }
}
