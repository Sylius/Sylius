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
use Sylius\Bundle\AdminBundle\Event\CustomerShowMenuBuilderEvent;
use Sylius\Bundle\AdminBundle\Menu\CustomerShowMenuBuilder;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class CustomerShowMenuBuilderSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($factory, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerShowMenuBuilder::class);
    }

    function it_creates_a_customer_show_menu_for_customer_with_user(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
        ItemInterface $menu,
        CustomerInterface $customer,
        UserInterface $user
    ) {
        $factory->createItem('root')->willReturn($menu);

        $customer->getId()->willReturn(7);
        $customer->getUser()->willReturn($user);
        $user->getId()->willReturn(4);

        $menu->setExtra('column_id', 'actions')->shouldBeCalled()->willReturn($menu);

        $menu
            ->addChild('update', [
                'route' => 'sylius_admin_customer_update',
                'routeParameters' => ['id' => 7]
            ])
            ->shouldBeCalled()
            ->willReturn($menu)
        ;
        $menu->setAttribute('type', 'edit')->shouldBeCalled()->willReturn($menu);
        $menu->setLabel('sylius.ui.edit')->shouldBeCalled()->willReturn($menu);

        $menu
            ->addChild('order_index', [
                'route' => 'sylius_admin_customer_order_index',
                'routeParameters' => ['id' => 7]
            ])
            ->shouldBeCalled()
            ->willReturn($menu)
        ;
        $menu->setAttribute('type', 'show')->shouldBeCalled()->willReturn($menu);
        $menu->setLabel('sylius.ui.show_orders')->shouldBeCalled()->willReturn($menu);

        $menu
            ->addChild('user_delete', [
                'route' => 'sylius_admin_shop_user_delete',
                'routeParameters' => ['id' => 4]
            ])
            ->shouldBeCalled()
            ->willReturn($menu)
        ;
        $menu->setAttribute('type', 'delete')->shouldBeCalled()->willReturn($menu);
        $menu->setAttribute('resource_id', 7)->shouldBeCalled()->willReturn($menu);
        $menu->setLabel('sylius.ui.delete')->shouldBeCalled()->willReturn($menu);

        $eventDispatcher
            ->dispatch('sylius.menu.admin.customer.show', Argument::type(CustomerShowMenuBuilderEvent::class))
            ->shouldBeCalled()
        ;

        $this->createMenu(['customer' => $customer])->shouldReturn($menu);
    }

    function it_creates_a_customer_show_menu_for_customer_without_user(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
        ItemInterface $menu,
        CustomerInterface $customer
    ) {
        $factory->createItem('root')->willReturn($menu);

        $customer->getId()->willReturn(7);
        $customer->getUser()->willReturn(null);

        $menu->setExtra('column_id', 'no-account')->shouldBeCalled()->willReturn($menu);

        $menu
            ->addChild('order_index', [
                'route' => 'sylius_admin_customer_order_index',
                'routeParameters' => ['id' => 7]
            ])
            ->shouldBeCalled()
            ->willReturn($menu)
        ;
        $menu->setAttribute('type', 'show')->shouldBeCalled()->willReturn($menu);
        $menu->setLabel('sylius.ui.show_orders')->shouldBeCalled()->willReturn($menu);

        $eventDispatcher
            ->dispatch('sylius.menu.admin.customer.show', Argument::type(CustomerShowMenuBuilderEvent::class))
            ->shouldBeCalled()
        ;

        $this->createMenu(['customer' => $customer])->shouldReturn($menu);
    }

    function it_returns_an_empty_customer_show_menu_when_there_is_no_customer_in_options(
        FactoryInterface $factory,
        ItemInterface $menu
    ) {

        $factory->createItem('root')->willReturn($menu);

        $this->createMenu([])->shouldReturn($menu);
    }
}
