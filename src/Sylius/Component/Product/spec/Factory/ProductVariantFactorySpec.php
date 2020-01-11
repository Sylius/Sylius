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
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductVariantFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    function it_is_a_resource_factory(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_implements_variant_factory_interface(): void
    {
        $this->shouldImplement(ProductVariantFactoryInterface::class);
    }

    function it_creates_new_variant(FactoryInterface $factory, ProductVariantInterface $variant): void
    {
        $factory->createNew()->willReturn($variant);

        $this->createNew()->shouldReturn($variant);
    }

    function it_creates_a_variant_and_assigns_a_product_to_it(
        FactoryInterface $factory,
        ProductInterface $product,
        ProductVariantInterface $variant
    ): void {
        $factory->createNew()->willReturn($variant);
        $variant->setProduct($product)->shouldBeCalled();

        $this->createForProduct($product)->shouldReturn($variant);
    }
}
