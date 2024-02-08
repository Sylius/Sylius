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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionScope;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScope;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

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

    function it_adds_violation_if_catalog_promotion_scope_has_not_existing_variants_configured(
        ProductVariantRepositoryInterface $variantRepository,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $variantRepository->findOneBy(['code' => 'not_existing_variant'])->willReturn(null);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_variants.invalid_variants')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.variants')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['variants' => ['not_existing_variant']], new CatalogPromotionScope(), $executionContext);
    }

    function it_does_nothing_if_catalog_promotion_scope_is_valid(
        ProductVariantRepositoryInterface $variantRepository,
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
    ): void {
        $variantRepository->findOneBy(['code' => 'first_variant'])->willReturn($firstVariant);
        $variantRepository->findOneBy(['code' => 'second_variant'])->willReturn($secondVariant);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_variants.not_empty')->shouldNotBeCalled();
        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_variants.invalid_variants')->shouldNotBeCalled();

        $this->validate(['variants' => ['first_variant', 'second_variant']], new CatalogPromotionScope(), $executionContext);
    }
}
