<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use PhpSpec\ObjectBehavior;
use SM\StateMachine\StateMachineInterface as WinzouStateMachineInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderShowMenuBuilderEventSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $factory,
        ItemInterface $menu,
        OrderInterface $order,
        WinzouStateMachineInterface $stateMachine,
    ): void {
        $this->beConstructedWith($factory, $menu, $order, $stateMachine);
    }

    function it_is_a_menu_builder_event(): void
    {
        $this->shouldHaveType(MenuBuilderEvent::class);
    }

    function it_has_an_order(OrderInterface $order): void
    {
        $this->getOrder()->shouldReturn($order);
    }

    function it_has_a_state_machine(WinzouStateMachineInterface $stateMachine): void
    {
        $this->getStateMachine()->shouldReturn($stateMachine);
    }

    function it_allows_to_pass_the_new_state_machine_abstraction(
        FactoryInterface $factory,
        ItemInterface $menu,
        OrderInterface $order,
        StateMachineInterface $newStateMachineAbstraction,
    ): void {
        $this->beConstructedWith($factory, $menu, $order, $newStateMachineAbstraction);
        $this->getStateMachine()->shouldReturn($newStateMachineAbstraction);
    }
}
