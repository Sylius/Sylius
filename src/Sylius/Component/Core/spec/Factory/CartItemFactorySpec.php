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

namespace spec\Sylius\Component\Core\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CartItemFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedFactory, ProductVariantResolverInterface $variantResolver): void
    {
        $this->beConstructedWith($decoratedFactory, $variantResolver);
    }

    function it_implements_a_cart_item_factory_interface(): void
    {
        $this->shouldImplement(CartItemFactoryInterface::class);
    }

    function it_is_a_resource_factory(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_creates_a_new_cart_item(FactoryInterface $decoratedFactory, OrderItemInterface $cartItem): void
    {
        $decoratedFactory->createNew()->willReturn($cartItem);

        $this->createNew()->shouldReturn($cartItem);
    }

    function it_creates_a_cart_item_and_assigns_a_product_variant(
        FactoryInterface $decoratedFactory,
        ProductVariantResolverInterface $variantResolver,
        OrderItemInterface $cartItem,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
    ): void {
        $decoratedFactory->createNew()->willReturn($cartItem);
        $variantResolver->getVariant($product)->willReturn($productVariant);

        $cartItem->setVariant($productVariant)->shouldBeCalled();

        $this->createForProduct($product)->shouldReturn($cartItem);
    }

    function it_creates_a_cart_item_for_given_cart(
        FactoryInterface $decoratedFactory,
        OrderItemInterface $cartItem,
        OrderInterface $order,
    ): void {
        $decoratedFactory->createNew()->willReturn($cartItem);

        $cartItem->setOrder($order)->shouldBeCalled();

        $this->createForCart($order)->shouldReturn($cartItem);
    }
}
