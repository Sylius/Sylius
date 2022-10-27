<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AfterCanceledOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\CancelPaymentCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class CancelPaymentCallbackSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusPaymentWorkflow): void
    {
        $this->beConstructedWith($syliusPaymentWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CancelPaymentCallback::class);
    }

    function it_is_called_after_canceled_order(): void
    {
        $this->shouldImplement(AfterCanceledOrderCallbackInterface::class);
    }

    function it_cancels_payment(
        OrderInterface $order,
        WorkflowInterface $syliusPaymentWorkflow,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
        $order->getPayments()->willReturn(new ArrayCollection([
            $firstPayment->getWrappedObject(),
            $secondPayment->getWrappedObject(),
        ]));

        $syliusPaymentWorkflow->can($firstPayment, 'cancel')->willReturn(false);
        $syliusPaymentWorkflow->can($secondPayment, 'cancel')->willReturn(true);

        $syliusPaymentWorkflow->apply($firstPayment, 'cancel')->shouldNotBeCalled();
        $syliusPaymentWorkflow->apply($secondPayment, 'cancel')->shouldBeCalled();

        $this->call($order);
    }
}
