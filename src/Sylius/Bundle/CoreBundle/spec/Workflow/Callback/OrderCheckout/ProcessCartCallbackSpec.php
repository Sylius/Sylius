<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterAddressedCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterSelectedPaymentCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterSelectedShippingCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterSkippedPaymentCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterSkippedShippingCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\ProcessCartCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class ProcessCartCallbackSpec extends ObjectBehavior
{
    function let(OrderProcessorInterface $orderProcessor): void
    {
        $this->beConstructedWith($orderProcessor);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ProcessCartCallback::class);
    }

    function it_is_called_after_selected_shipping(): void
    {
        $this->shouldImplement(AfterSelectedShippingCallbackInterface::class);
    }

    function it_is_called_after_addressed(): void
    {
        $this->shouldImplement(AfterAddressedCallbackInterface::class);
    }

    function it_is_called_after_selected_payment(): void
    {
        $this->shouldImplement(AfterSelectedPaymentCallbackInterface::class);
    }

    function it_is_called_after_skipped_shipping(): void
    {
        $this->shouldImplement(AfterSkippedShippingCallbackInterface::class);
    }

    function it_is_called_after_skipped_payment(): void
    {
        $this->shouldImplement(AfterSkippedPaymentCallbackInterface::class);
    }

    function it_processes_cart(
        OrderInterface $order,
        OrderProcessorInterface $orderProcessor,
    ): void {
        $orderProcessor->process($order)->shouldBeCalled();

        $this->call($order);
    }
}
