<?php

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Order\CreatePaymentListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

class CreatePaymentListenerSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusPaymentWorkflow): void
    {
        $this->beConstructedWith($syliusPaymentWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CreatePaymentListener::class);
    }

    function it_creates_payment(
        Event $event,
        OrderInterface $order,
        WorkflowInterface $syliusPaymentWorkflow,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
        $event->getSubject()->willReturn($order);

        $order->getPayments()->willReturn(new ArrayCollection([
            $firstPayment->getWrappedObject(),
            $secondPayment->getWrappedObject(),
        ]));

        $syliusPaymentWorkflow->apply($firstPayment, 'create')->shouldBeCalled();
        $syliusPaymentWorkflow->apply($secondPayment, 'create')->shouldBeCalled();

        $this->createPayment($event);
    }
}
