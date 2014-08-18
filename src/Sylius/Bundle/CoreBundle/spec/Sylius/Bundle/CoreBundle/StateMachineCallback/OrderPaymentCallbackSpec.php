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
    public function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\StateMachineCallback\OrderPaymentCallback');
    }

    public function it_dispatches_event_on_payment_update(
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
        $sm->apply(OrderTransitions::SYLIUS_CONFIRM, true)->shouldBeCalled();

        $this->updateOrderOnPayment($payment);
    }
}
