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

namespace spec\Sylius\Bundle\CoreBundle\Validator\CatalogPromotionScope;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CatalogPromotionScope;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ForVariantsScopeValidatorSpec extends ObjectBehavior
{
    function let(ProductVariantRepositoryInterface $variantRepository): void
    {
        $this->beConstructedWith($variantRepository);
    }

    function it_is_a_scope_validator(): void
    {
        $this->shouldHaveType(ScopeValidatorInterface::class);
    }

    function it_prepares_array_with_violation_if_catalog_promotion_scope_does_not_have_variants_key_configured(): void
    {
        $this
            ->validate([], new CatalogPromotionScope())
            ->shouldReturn(['configuration.variants' => 'sylius.catalog_promotion_scope.for_variants.not_empty'])
        ;
    }

    function it_prepares_array_with_violation_if_catalog_promotion_scope_has_empty_variants_configured(): void
    {
        $this
            ->validate(['variants' => []], new CatalogPromotionScope())
            ->shouldReturn(['configuration.variants' => 'sylius.catalog_promotion_scope.for_variants.not_empty'])
        ;
    }

    function it_prepares_array_with_violation_if_catalog_promotion_scope_has_not_existing_variants_configured(
        ProductVariantRepositoryInterface $variantRepository
    ): void {
        $variantRepository->findOneBy(['code' => 'not_existing_variant'])->willReturn(null);

        $this
            ->validate(['variants' => ['not_existing_variant']], new CatalogPromotionScope())
            ->shouldReturn(['configuration.variants' => 'sylius.catalog_promotion_scope.for_variants.invalid_variants'])
        ;
    }

    function it_returns_an_empty_array_if_catalog_promotion_scope_is_valid(
        ProductVariantRepositoryInterface $variantRepository,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant
    ): void {
        $variantRepository->findOneBy(['code' => 'first_variant'])->willReturn($firstVariant);
        $variantRepository->findOneBy(['code' => 'second_variant'])->willReturn($secondVariant);

        $this->validate(['variants' => ['first_variant', 'second_variant']], new CatalogPromotionScope())->shouldReturn([]);
    }
}
