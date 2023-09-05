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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

final class DefaultProductVariantResolverSpec extends ObjectBehavior
{
    function let(ProductVariantRepositoryInterface $productVariantRepository): void
    {
        $this->beConstructedWith($productVariantRepository);
    }

    function it_implements_variant_resolver_interface(): void
    {
        $this->shouldImplement(ProductVariantResolverInterface::class);
    }

    function it_returns_first_variant(
        ProductInterface $product,
        ProductVariantInterface $variant,
        Collection $variants,
        ProductVariantRepositoryInterface $productVariantRepository,
    ): void {
        $product->getId()->willReturn(1);
        $productVariantRepository->findOneBy([
            'product' => 1,
            'enabled' => true,
        ])->willReturn($variant);

        $this->getVariant($product)->shouldReturn($variant);
    }

    function it_returns_null_if_first_variant_is_not_defined(
        Collection $variants,
        ProductInterface $product,
        ProductVariantRepositoryInterface $productVariantRepository,
    ): void
    {
        $product->getId()->willReturn(1);
        $productVariantRepository->findOneBy([
            'product' => 1,
            'enabled' => true,
        ])->willReturn(null);

        $this->getVariant($product)->shouldReturn(null);
    }
}
