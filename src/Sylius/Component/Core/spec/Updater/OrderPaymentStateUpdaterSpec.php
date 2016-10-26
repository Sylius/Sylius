<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Updater;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Updater\OrderPaymentStateUpdater;
use Sylius\Component\Core\Updater\OrderUpdaterInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderPaymentStateUpdaterSpec extends ObjectBehavior
{
    function let(FactoryInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPaymentStateUpdater::class);
    }

    function it_implements_order_updater_interface()
    {
        $this->shouldImplement(OrderUpdaterInterface::class);
    }

    function it_refunds_order_if_all_its_payments_are_refunded(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
        StateMachineInterface $stateMachine
    ) {
        $order->getPayments()->willReturn([$firstPayment, $secondPayment]);

        $firstPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_REFUND)->shouldBeCalled();

        $this->update($order);
    }

    function it_does_nothing_if_at_least_one_payment_is_not_refunded(
        FactoryInterface $stateMachineFactory,
        OrderInterface $order,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment
    ) {
        $order->getPayments()->willReturn([$firstPayment, $secondPayment]);

        $firstPayment->getState()->willReturn(PaymentInterface::STATE_REFUNDED);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_NEW);

        $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->shouldNotBeCalled();

        $this->update($order);
    }
}
