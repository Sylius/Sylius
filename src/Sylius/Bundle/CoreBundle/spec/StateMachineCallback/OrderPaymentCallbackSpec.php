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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderPaymentCallbackSpec extends ObjectBehavior
{
    function let(FactoryInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\StateMachineCallback\OrderPaymentCallback');
    }

    function it_dispatches_event_on_payment_update_and_will_update_order_state(
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $payment->getOrder()->willReturn($order);
        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);
        $payment->getAmount()->willReturn(100);
        $payments = new ArrayCollection([$payment->getWrappedObject()]);

        $order->getPayments()->willReturn($payments);
        $order->getTotal()->willReturn(100);

        $stateMachineFactory->get($order, OrderTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->apply(OrderTransitions::TRANSITION_FULFILL, true)->shouldBeCalled();

        $this->updateOrderOnPayment($payment);
    }
}
