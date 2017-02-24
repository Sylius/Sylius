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
use Sylius\Bundle\AdminBundle\Event\CustomerShowMenuBuilderEvent;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class CustomerShowMenuBuilderEventSpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, ItemInterface $menu, CustomerInterface $customer)
    {
        $this->beConstructedWith($factory, $menu, $customer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerShowMenuBuilderEvent::class);
    }

    function it_is_a_menu_builder_event()
    {
        $this->shouldHaveType(MenuBuilderEvent::class);
    }

    function it_has_a_customer(CustomerInterface $customer)
    {
        $this->getCustomer()->shouldReturn($customer);
    }
}
