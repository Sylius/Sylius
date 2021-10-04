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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CatalogPromotionScope;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CatalogPromotionScopeValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
        ProductVariantRepositoryInterface $variantRepository,
        TaxonRepositoryInterface $taxonRepository
    ): void {
        $this->beConstructedWith($variantRepository, $taxonRepository);

        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_adds_violation_if_catalog_promotion_scope_has_invalid_type(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        CatalogPromotionScopeInterface $rule
    ): void {
        $rule->getType()->willReturn('wrong_type');

        $executionContext->buildViolation('sylius.catalog_promotion_scope.invalid_type')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('type')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($rule, new CatalogPromotionScope());
    }

    function it_adds_violation_if_catalog_promotion_scope_does_not_have_variants_key_configured(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        CatalogPromotionScopeInterface $rule
    ): void {
        $rule->getType()->willReturn(CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS);
        $rule->getConfiguration()->willReturn([]);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_variants.not_empty')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.variants')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($rule, new CatalogPromotionScope());
    }

    function it_adds_violation_if_catalog_promotion_scope_does_not_have_taxons_key_configured(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        CatalogPromotionScopeInterface $rule
    ): void {
        $rule->getType()->willReturn(CatalogPromotionScopeInterface::TYPE_FOR_TAXONS);
        $rule->getConfiguration()->willReturn([]);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_taxons.not_empty')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.taxons')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($rule, new CatalogPromotionScope());
    }

    function it_adds_violation_if_catalog_promotion_scope_has_empty_variants_configured(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        CatalogPromotionScopeInterface $rule
    ): void {
        $rule->getType()->willReturn(CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS);
        $rule->getConfiguration()->willReturn(['variants' => []]);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_variants.not_empty')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.variants')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($rule, new CatalogPromotionScope());
    }

    function it_adds_violation_if_catalog_promotion_scope_has_not_existing_variants_configured(
        ExecutionContextInterface $executionContext,
        ProductVariantRepositoryInterface $variantRepository,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        CatalogPromotionScopeInterface $rule
    ): void {
        $rule->getType()->willReturn(CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS);
        $rule->getConfiguration()->willReturn(['variants' => ['not_existing_variant']]);

        $variantRepository->findOneBy(['code' => 'not_existing_variant'])->willReturn(null);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_variants.invalid_variants')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.variants')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($rule, new CatalogPromotionScope());
    }

    function it_adds_violation_if_catalog_promotion_scope_has_not_existing_taxons_configured(
        ExecutionContextInterface $executionContext,
        TaxonRepositoryInterface $taxonRepository,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        CatalogPromotionScopeInterface $rule
    ): void {
        $rule->getType()->willReturn(CatalogPromotionScopeInterface::TYPE_FOR_TAXONS);
        $rule->getConfiguration()->willReturn(['taxons' => ['not_existing_variant']]);

        $taxonRepository->findOneBy(['code' => 'not_existing_variant'])->willReturn(null);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_taxons.invalid_taxons')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.taxons')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($rule, new CatalogPromotionScope());
    }

    function it_does_nothing_if_catalog_promotion_scope_is_valid(
        ExecutionContextInterface $executionContext,
        ProductVariantRepositoryInterface $variantRepository,
        CatalogPromotionScopeInterface $rule,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant
    ): void {
        $rule->getType()->willReturn(CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS);
        $rule->getConfiguration()->willReturn(['variants' => ['first_variant', 'second_variant']]);

        $variantRepository->findOneBy(['code' => 'first_variant'])->willReturn($firstVariant);
        $variantRepository->findOneBy(['code' => 'second_variant'])->willReturn($secondVariant);

        $executionContext->buildViolation('sylius.catalog_promotion_scope.invalid_type')->shouldNotBeCalled();
        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_variants.not_empty')->shouldNotBeCalled();
        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_variants.invalid_variants')->shouldNotBeCalled();

        $this->validate($rule, new CatalogPromotionScope());
    }
}
