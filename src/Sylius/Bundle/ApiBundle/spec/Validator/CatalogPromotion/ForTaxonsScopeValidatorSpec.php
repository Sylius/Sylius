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
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScope;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ForTaxonsScopeValidatorSpec extends ObjectBehavior
{
    function let(ScopeValidatorInterface $baseScopeValidator, SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($baseScopeValidator, $sectionProvider);
    }

    function it_is_a_scope_validator(): void
    {
        $this->shouldHaveType(ScopeValidatorInterface::class);
    }

    function it_adds_violation_if_catalog_promotion_scope_does_not_have_taxons_key_configured(
        ScopeValidatorInterface $baseScopeValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $constraint = new CatalogPromotionScope();

        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_taxons.not_empty')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.taxons')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $baseScopeValidator->validate(Argument::any())->shouldNotBeCalled();

        $this->validate([], $constraint, $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_scope_does_not_have_taxons_key_defined(
        ScopeValidatorInterface $baseScopeValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $constraint = new CatalogPromotionScope();

        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_taxons.not_empty')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.taxons')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $baseScopeValidator->validate(Argument::any())->shouldNotBeCalled();

        $this->validate(['taxons' => []], $constraint, $executionContext);
    }

    function it_does_nothing_if_catalog_promotion_scope_is_valid(
        ScopeValidatorInterface $baseScopeValidator,
        SectionProviderInterface $sectionProvider,
        ExecutionContextInterface $executionContext,
    ): void {
        $constraint = new CatalogPromotionScope();

        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $executionContext->buildViolation('sylius.catalog_promotion_scope.for_taxons.not_empty')->shouldNotBeCalled();

        $baseScopeValidator->validate(['taxons' => ['taxon']], $constraint, $executionContext)->shouldBeCalled();

        $this->validate(['taxons' => ['taxon']], $constraint, $executionContext);
    }
}
