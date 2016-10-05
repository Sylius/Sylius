<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Updater;

use PhpSpec\ObjectBehavior;
use SM\Factory\Factory;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Updater\UnpaidOrdersStateUpdater;
use Sylius\Component\Order\Updater\UnpaidOrdersStateUpdaterInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 *
 * @mixin UnpaidOrdersStateUpdater
 */
final class UnpaidOrdersStateUpdaterSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, Factory $stateMachineFactory)
    {
        $this->beConstructedWith($orderRepository, $stateMachineFactory, '10 months');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UnpaidOrdersStateUpdater::class);
    }

    function it_implements_expired_orders_state_updater_interface()
    {
        $this->shouldImplement(UnpaidOrdersStateUpdaterInterface::class);
    }

    function it_cancels_unpaid_orders(
        Factory $stateMachineFactory,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $firstOrderStateMachine,
        StateMachineInterface $secondOrderStateMachine
    ) {
        $orderRepository->findOrdersUnpaidSince(new \DateTime('-10 months'))->willReturn([
           $firstOrder,
           $secondOrder
        ]);

        $stateMachineFactory->get($firstOrder, 'sylius_order')->willReturn($firstOrderStateMachine);
        $stateMachineFactory->get($secondOrder, 'sylius_order')->willReturn($secondOrderStateMachine);

        $firstOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();
        $secondOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $this->cancel();
    }
}
