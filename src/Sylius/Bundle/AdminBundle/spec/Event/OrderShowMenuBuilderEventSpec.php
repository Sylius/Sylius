<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use PhpSpec\ObjectBehavior;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderShowMenuBuilderEventSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $factory,
        ItemInterface $menu,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ) {
        $this->beConstructedWith($factory, $menu, $order, $stateMachine);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderShowMenuBuilderEvent::class);
    }

    function it_is_a_menu_builder_event()
    {
        $this->shouldHaveType(MenuBuilderEvent::class);
    }

    function it_has_an_order(OrderInterface $order)
    {
        $this->getOrder()->shouldReturn($order);
    }

    function it_has_a_state_machine(StateMachineInterface $stateMachine)
    {
        $this->getStateMachine()->shouldReturn($stateMachine);
    }
}
