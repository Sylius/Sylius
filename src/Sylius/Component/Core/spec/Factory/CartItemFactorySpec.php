<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Factory\CartItemFactory;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class CartItemFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedFactory, ProductVariantResolverInterface $variantResolver)
    {
        $this->beConstructedWith($decoratedFactory, $variantResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartItemFactory::class);
    }

    function it_implements_a_cart_item_factory_interface()
    {
        $this->shouldImplement(CartItemFactoryInterface::class);
    }

    function it_is_a_resource_factory()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_creates_a_new_cart_item(FactoryInterface $decoratedFactory, OrderItemInterface $cartItem)
    {
        $decoratedFactory->createNew()->willReturn($cartItem);

        $this->createNew()->shouldReturn($cartItem);
    }

    function it_creates_a_cart_item_and_assigns_a_product_variant(
        FactoryInterface $decoratedFactory,
        ProductVariantResolverInterface $variantResolver,
        OrderItemInterface $cartItem,
        ProductInterface $product,
        ProductVariantInterface $productVariant
    ) {
        $decoratedFactory->createNew()->willReturn($cartItem);
        $variantResolver->getVariant($product)->willReturn($productVariant);

        $cartItem->setVariant($productVariant)->shouldBeCalled();

        $this->createForProduct($product)->shouldReturn($cartItem);
    }
}
