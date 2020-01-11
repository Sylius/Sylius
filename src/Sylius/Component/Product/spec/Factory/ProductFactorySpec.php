<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Product\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductFactorySpec extends ObjectBehavior
{
    function let(
        FactoryInterface $factory,
        FactoryInterface $variantFactory
    ): void {
        $this->beConstructedWith($factory, $variantFactory);
    }

    function it_implements_product_factory_interface(): void
    {
        $this->shouldImplement(ProductFactoryInterface::class);
    }

    function it_creates_new_product(FactoryInterface $factory, ProductInterface $product): void
    {
        $factory->createNew()->willReturn($product);

        $this->createNew()->shouldReturn($product);
    }

    function it_creates_new_product_with_variant(
        FactoryInterface $factory,
        FactoryInterface $variantFactory,
        ProductInterface $product,
        ProductVariantInterface $variant
    ): void {
        $variantFactory->createNew()->willReturn($variant);

        $factory->createNew()->willReturn($product);
        $product->addVariant($variant)->shouldBeCalled();

        $this->createWithVariant()->shouldReturn($product);
    }
}
