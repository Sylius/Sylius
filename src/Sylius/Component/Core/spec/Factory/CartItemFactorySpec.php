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
use Sylius\Component\Core\Factory\ActionFactory;
use Sylius\Component\Core\Factory\ActionFactoryInterface;
use Sylius\Component\Core\Factory\CartItemFactory;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Promotion\Action\FixedDiscountAction;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountAction;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountAction;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountAction;
use Sylius\Component\Core\Promotion\Action\ShippingDiscountAction;
use Sylius\Component\Product\Model\VariantInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Variation\Resolver\VariantResolverInterface;

/**
 * @mixin CartItemFactory
 *
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class CartItemFactorySpec extends ObjectBehavior
{
    function let(
        FactoryInterface $decoratedFactory,
        RepositoryInterface $productRepository,
        VariantResolverInterface $variantResolver
    ) {
        $this->beConstructedWith($decoratedFactory, $productRepository, $variantResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Factory\CartItemFactory');
    }

    function it_implements_cart_item_factory_interface()
    {
        $this->shouldImplement(CartItemFactoryInterface::class);
    }

    function it_is_a_resource_factory()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_creates_new_cart_item(FactoryInterface $decoratedFactory, OrderItemInterface $cartItem)
    {
        $decoratedFactory->createNew()->willReturn($cartItem);

        $this->createNew()->shouldReturn($cartItem);
    }

    function it_throws_an_exception_when_product_is_not_found(RepositoryInterface $productRepository)
    {
        $productRepository->find(7)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('createForProductWithId', [7])
        ;
    }

    function it_creates_a_cart_item_and_assigns_a_product_variant(
        FactoryInterface $decoratedFactory,
        RepositoryInterface $productRepository,
        VariantResolverInterface $variantResolver,
        OrderItemInterface $cartItem,
        ProductInterface $product,
        ProductVariantInterface $productVariant
    ) {
        $decoratedFactory->createNew()->willReturn($cartItem);

        $productRepository->find(7)->willReturn($product);
        $variantResolver->getVariant($product)->willReturn($productVariant);

        $cartItem->setVariant($productVariant)->shouldBeCalled();

        $this->createForProductWithId(7)->shouldReturn($cartItem);
    }
}
