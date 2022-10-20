<?php

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\AfterPlacedOrder;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\AfterPlacedOrder\CreatePaymentCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class CreatePaymentCallbackSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusPaymentWorkflow): void
    {
        $this->beConstructedWith($syliusPaymentWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CreatePaymentCallback::class);
    }

    function it_creates_payment(
        OrderInterface $order,
        WorkflowInterface $syliusPaymentWorkflow,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
        $order->getPayments()->willReturn(new ArrayCollection([
            $firstPayment->getWrappedObject(),
            $secondPayment->getWrappedObject(),
        ]));

        $syliusPaymentWorkflow->apply($firstPayment, 'create')->shouldBeCalled();
        $syliusPaymentWorkflow->apply($secondPayment, 'create')->shouldBeCalled();

        $this->run($order);
    }
}
