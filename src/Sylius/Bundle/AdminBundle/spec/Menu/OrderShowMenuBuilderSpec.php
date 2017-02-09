<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\AdminBundle\Menu\OrderShowMenuBuilder;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderShowMenuBuilderSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
        StateMachineFactoryInterface $stateMachineFactory
    ) {
        $this->beConstructedWith($factory, $eventDispatcher, $stateMachineFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderShowMenuBuilder::class);
    }

    function it_creates_an_order_show_menu(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
        StateMachineFactoryInterface $stateMachineFactory,
        ItemInterface $menu,
        StateMachineInterface $stateMachine,
        OrderInterface $order
    ) {
        $factory->createItem('root')->willReturn($menu);

        $order->getId()->willReturn(7);

        $menu
            ->addChild('order_history', [
                'route' => 'sylius_admin_order_history',
                'routeParameters' => ['id' => 7]
            ])
            ->shouldBeCalled()
            ->willReturn($menu)
        ;
        $menu->setAttribute('type', 'link')->shouldBeCalled()->willReturn($menu);
        $menu->setLabel('sylius.ui.history')->shouldBeCalled()->willReturn($menu);
        $menu->setLabelAttribute('icon', 'history')->shouldBeCalled()->willReturn($menu);

        $stateMachineFactory->get($order, OrderTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderTransitions::TRANSITION_CANCEL)->willReturn(true);

        $menu
            ->addChild('cancel', [
                'route' => 'sylius_admin_order_cancel',
                'routeParameters' => ['id' => 7]
            ])
            ->shouldBeCalled()
            ->willReturn($menu)
        ;
        $menu->setAttribute('type', 'transition')->shouldBeCalled()->willReturn($menu);
        $menu->setLabel('sylius.ui.cancel')->shouldBeCalled()->willReturn($menu);
        $menu->setLabelAttribute('icon', 'ban')->shouldBeCalled()->willReturn($menu);
        $menu->setLabelAttribute('color', 'yellow')->shouldBeCalled()->willReturn($menu);

        $eventDispatcher
            ->dispatch('sylius.menu.admin.order.show', Argument::type(MenuBuilderEvent::class))
            ->shouldBeCalled()
        ;

        $this->createMenu(['order' => $order])->shouldReturn($menu);
    }

    function it_creates_an_order_show_menu_when_cancel_transition_is_impossible(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
        StateMachineFactoryInterface $stateMachineFactory,
        ItemInterface $menu,
        StateMachineInterface $stateMachine,
        OrderInterface $order
    ) {
        $factory->createItem('root')->willReturn($menu);

        $order->getId()->willReturn(7);

        $menu
            ->addChild('order_history', [
                'route' => 'sylius_admin_order_history',
                'routeParameters' => ['id' => 7]
            ])
            ->shouldBeCalled()
            ->willReturn($menu)
        ;
        $menu->setAttribute('type', 'link')->shouldBeCalled()->willReturn($menu);
        $menu->setLabel('sylius.ui.history')->shouldBeCalled()->willReturn($menu);
        $menu->setLabelAttribute('icon', 'history')->shouldBeCalled()->willReturn($menu);

        $stateMachineFactory->get($order, OrderTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderTransitions::TRANSITION_CANCEL)->willReturn(false);

        $eventDispatcher
            ->dispatch('sylius.menu.admin.order.show', Argument::type(MenuBuilderEvent::class))
            ->shouldBeCalled()
        ;

        $this->createMenu(['order' => $order])->shouldReturn($menu);
    }

    function it_returns_an_empty_order_show_menu_when_there_is_no_order_in_options(
        FactoryInterface $factory,
        ItemInterface $menu
    ) {

        $factory->createItem('root')->willReturn($menu);

        $this->createMenu([])->shouldReturn($menu);
    }
}
