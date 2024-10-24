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

namespace spec\Sylius\Component\Product\Resolver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

final class CompositeProductVariantResolverSpec extends ObjectBehavior
{
    function let(ProductVariantResolverInterface $firstResolver, ProductVariantResolverInterface $secondResolver): void
    {
        $this->beConstructedWith([$firstResolver, $secondResolver]);
    }

    function it_implements_variant_resolver_interface(): void
    {
        $this->shouldImplement(ProductVariantResolverInterface::class);
    }

    function it_returns_null_when_no_resolver_returns_a_variant(
        ProductVariantResolverInterface $firstResolver,
        ProductVariantResolverInterface $secondResolver,
        ProductInterface $product,
    ): void {
        $firstResolver->getVariant($product)->shouldBeCalled()->willReturn(null);
        $secondResolver->getVariant($product)->shouldBeCalled()->willReturn(null);

        $this->getVariant($product)->shouldReturn(null);
    }

    function it_returns_first_resolved_variant(
        ProductVariantResolverInterface $firstResolver,
        ProductVariantResolverInterface $secondResolver,
        ProductInterface $product,
        ProductVariantInterface $variant,
    ): void {
        $firstResolver->getVariant($product)->shouldBeCalled()->willReturn($variant);
        $secondResolver->getVariant(Argument::any())->shouldNotBeCalled();

        $this->getVariant($product)->shouldReturn($variant);
    }
}
