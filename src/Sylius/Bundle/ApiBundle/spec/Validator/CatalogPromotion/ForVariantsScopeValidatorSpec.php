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

namespace spec\Sylius\Bundle\ApiBundle\Validator\CatalogPromotion;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScope;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ForVariantsScopeValidatorSpec extends ObjectBehavior
{
    function let(ScopeValidatorInterface $baseScopeValidator, SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($baseScopeValidator, $sectionProvider);
    }

    function it_is_a_scope_validator(): void
    {
        $this->shouldHaveType(ScopeValidatorInterface::class);
    }

    function it_just_fallbacks_to_default_validator_if_it_is_not_admin_api_section(
        ScopeValidatorInterface $baseScopeValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
    ): void {
        $constraint = new CatalogPromotionScope();

        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $baseScopeValidator->validate([], $constraint, $executionContext)->shouldBeCalled();

        $executionContext->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate([], $constraint, $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_scope_does_not_have_variants_key_configured(
        ScopeValidatorInterface $baseScopeValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_variants.not_empty')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.variants')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $baseScopeValidator->validate(Argument::any())->shouldNotBeCalled();

        $this->validate([], new CatalogPromotionScope(), $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_scope_has_empty_variants_configured(
        ScopeValidatorInterface $baseScopeValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_variants.not_empty')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.variants')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $baseScopeValidator->validate(Argument::any())->shouldNotBeCalled();

        $this->validate(['variants' => []], new CatalogPromotionScope(), $executionContext);
    }

    function it_does_nothing_if_catalog_promotion_scope_is_valid(
        ScopeValidatorInterface $baseScopeValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
    ): void {
        $constraint = new CatalogPromotionScope();

        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_variants.not_empty')->shouldNotBeCalled();

        $baseScopeValidator->validate(['variants' => ['first_variant', 'second_variant']], $constraint, $executionContext);

        $this->validate(['variants' => ['first_variant', 'second_variant']], $constraint, $executionContext);
    }
}
