<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;

class InventoryAvailabilityResolverSpec extends ObjectBehavior
{
    function let(AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->beConstructedWith($availabilityChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Resolver\InventoryAvailabilityResolver');
    }

    function it_implements_item_resolver_interface()
    {
        $this->shouldImplement('Sylius\Component\Cart\Resolver\ItemResolverInterface');
    }

    function it_should_check_stock_is_sufficient(
        $availabilityChecker,
        OrderItemInterface $item,
        OrderInterface $order,
        ProductVariantInterface $variant
    ) {
        $item->getOrder()->willReturn($order);
        $item->getQuantity()->willReturn(1);

        $order->getItems()->willReturn(array());

        $item->getVariant()->willReturn($variant);

        $availabilityChecker->isStockSufficient($variant, 1)->willReturn(true);

        $this->resolve($item, array());
    }

    function it_throws_exception_if_stock_is_not_sufficient(
        $availabilityChecker,
        OrderItemInterface $item,
        OrderInterface $order,
        ProductVariantInterface $variant
    ) {
        $item->getOrder()->willReturn($order);
        $item->getQuantity()->willReturn(1);

        $order->getItems()->willReturn(array());

        $item->getVariant()->willReturn($variant);

        $availabilityChecker->isStockSufficient($variant, 1)->willReturn(false);

        $this
            ->shouldThrow('Sylius\Component\Cart\Resolver\ItemResolvingException')
            ->duringResolve($item, array())
        ;
    }
}
