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

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\GroupableInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;

class PriceResolverSpec extends ObjectBehavior
{
    function let(DelegatingCalculatorInterface $priceCalculator)
    {
        $this->beConstructedWith($priceCalculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Resolver\PriceResolver');
    }

    function it_implements_item_resolver_interface()
    {
        $this->shouldImplement('Sylius\Component\Cart\Resolver\ItemResolverInterface');
    }

    function it_should_recalculate_unit_price(
        $priceCalculator,
        OrderItemInterface $item,
        OrderInterface $order,
        ProductVariantInterface $variant
    ) {
        $context = array('quantity' => 1);

        $item->getOrder()->willReturn($order);
        $item->getQuantity()->willReturn(1);
        $item->getVariant()->willReturn($variant);

        $order->getUser()->willReturn(null);

        $priceCalculator->calculate($variant, $context)->willReturn(5000);

        $item->setUnitPrice(5000)->shouldBeCalled();

        $this->resolve($item, array());
    }

    function it_should_recalculate_unit_price_with_passing_user_groups(
        $priceCalculator,
        OrderItemInterface $item,
        OrderInterface $order,
        ProductVariantInterface $variant,
        GroupableInterface $user,
        Collection $groups
    ) {
        $context = array('quantity' => 1, 'groups' => array());

        $item->getOrder()->willReturn($order);
        $item->getQuantity()->willReturn(1);
        $item->getVariant()->willReturn($variant);

        $order->getUser()->willReturn($user);

        $user->getGroups()->willReturn($groups);

        $groups->toArray()->willReturn(array());

        $priceCalculator->calculate($variant, $context)->willReturn(15000);

        $item->setUnitPrice(15000)->shouldBeCalled();

        $this->resolve($item, array());
    }
}
