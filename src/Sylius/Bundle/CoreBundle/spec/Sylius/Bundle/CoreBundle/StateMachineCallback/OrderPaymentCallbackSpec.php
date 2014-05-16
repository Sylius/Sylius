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
use Finite\Factory\FactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\PaymentTransitions;
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

    function it_dispatches_event_on_payment_update(
        $factory,
        PaymentInterface $payment,
        OrderInterface $order,
        StateMachineInterface $sm,
        Collection $payments
    ) {
        $payment->getOrder()->willReturn($order);

        $order->getPayments()->willReturn($payments);
        $order->getTotal()->willReturn(0);

        $payments->getIterator()->willReturn(new \EmptyIterator());

        $factory->get($order, OrderTransitions::GRAPH)->willReturn($sm);
        $sm->apply(OrderTransitions::SYLIUS_CONFIRM)->shouldBeCalled();

        $this->updateOrderOnPayment($payment);
    }

    function it_dispatches_event_for_void_payments(
        $factory,
        PaymentInterface $payment,
        StateMachineInterface $sm,
        Collection $payments
    ) {
        $payments->getIterator()->willReturn(new \ArrayIterator(array($payment)));

        $factory->get($payment, PaymentTransitions::GRAPH)->willReturn($sm);
        $sm->apply(PaymentTransitions::SYLIUS_VOID)->shouldBeCalled();

        $this->voidPayments($payments, PaymentTransitions::SYLIUS_VOID);
    }
}
