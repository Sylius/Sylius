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

namespace spec\Sylius\Component\Product\Resolver;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

final class DefaultProductVariantResolverSpec extends ObjectBehavior
{
    function it_implements_variant_resolver_interface(): void
    {
        $this->shouldImplement(ProductVariantResolverInterface::class);
    }

    function it_returns_first_variant(
        ProductInterface $product,
        ProductVariantInterface $variant,
        Collection $variants
    ): void {
        $product->getEnabledVariants()->willReturn($variants);
        $variants->isEmpty()->willReturn(false);
        $variants->first()->willReturn($variant);

        $this->getVariant($product)->shouldReturn($variant);
    }

    function it_returns_null_if_first_variant_is_not_defined(Collection $variants, ProductInterface $product): void
    {
        $product->getEnabledVariants()->willReturn($variants);
        $variants->isEmpty()->willReturn(true);

        $this->getVariant($product)->shouldReturn(null);
    }
}
