<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class ProcessCartCallback implements AfterSelectedShippingCallbackInterface, AfterAddressedCallbackInterface, AfterSelectedPaymentCallbackInterface, AfterSkippedShippingCallbackInterface, AfterSkippedPaymentCallbackInterface
{
    public function __construct(private OrderProcessorInterface $orderProcessor)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->orderProcessor->process($order);
    }
}
