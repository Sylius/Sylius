<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout;

use PhpSpec\ObjectBehavior;
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

    function it_processes_cart(
        OrderInterface $order,
        OrderProcessorInterface $orderProcessor,
    ): void {
        $orderProcessor->process($order)->shouldBeCalled();

        $this->call($order);
    }
}
