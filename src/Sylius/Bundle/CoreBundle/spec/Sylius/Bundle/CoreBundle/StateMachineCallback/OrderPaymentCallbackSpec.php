<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\StateMachineCallback;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class OrderPaymentCallbackSpec extends ObjectBehavior
{
    function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\StateMachineCallback\OrderPaymentCallback');
    }

    function it_dispatches_event_on_payment_update_and_will_update_order_state(
        $factory,
        PaymentInterface $payment,
        OrderInterface $order,
        StateMachineInterface $sm
    ) {
        $payment->getOrder()->willReturn($order);
        $payment->getState()->willReturn(PaymentInterface::STATE_CANCELLED);

        $order->getTotal()->willReturn(0);
        $order->setPaymentState(PaymentInterface::STATE_CANCELLED)->shouldBeCalled();

        $factory->get($order, OrderTransitions::GRAPH)->willReturn($sm);
        $sm->apply(OrderTransitions::SYLIUS_CONFIRM, true)->shouldBeCalled();

        $this->updateOrderOnPayment($payment);
    }

    function it_dispatches_event_on_payment_update(
        $factory,
        PaymentInterface $payment,
        OrderInterface $order,
        StateMachineInterface $sm,
        Collection $payments,
        Collection $filteredPayments
    ) {
        $payment->getOrder()->willReturn($order);
        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);
        $payment->getAmount()->willReturn(1000);

        $order->getPayments()->willReturn($payments);
        $order->getTotal()->willReturn(1000);
        $order->setPaymentState(PaymentInterface::STATE_COMPLETED)->shouldBeCalled();

        $payments->filter(Argument::any())->willReturn($filteredPayments);
        $payments->count()->willReturn(1);

        $filteredPayments->count()->willReturn(1);

        $factory->get($order, OrderTransitions::GRAPH)->willReturn($sm);
        $sm->apply(OrderTransitions::SYLIUS_CONFIRM, true)->shouldBeCalled();

        $this->updateOrderOnPayment($payment);
    }
}
