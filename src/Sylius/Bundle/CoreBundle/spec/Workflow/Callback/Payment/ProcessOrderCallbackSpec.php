<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Payment;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\AfterCanceledPaymentCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\AfterFailedPaymentCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\ProcessOrderCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class ProcessOrderCallbackSpec extends ObjectBehavior
{
    function let(OrderProcessorInterface $orderProcessor): void
    {
        $this->beConstructedWith($orderProcessor);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ProcessOrderCallback::class);
    }

    function it_is_called_after_failed_payment(): void
    {
        $this->shouldImplement(AfterFailedPaymentCallbackInterface::class);
    }

    function it_is_called_after_canceled_payment(): void
    {
        $this->shouldImplement(AfterCanceledPaymentCallbackInterface::class);
    }

    function it_processes_the_order(
        PaymentInterface $payment,
        OrderInterface $order,
        OrderProcessorInterface $orderProcessor,
    ): void {
        $payment->getOrder()->willReturn($order);

        $orderProcessor->process($order)->shouldBeCalled();

        $this->call($payment);
    }
}
