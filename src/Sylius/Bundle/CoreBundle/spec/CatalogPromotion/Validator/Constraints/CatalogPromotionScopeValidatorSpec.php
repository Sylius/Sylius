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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\Constraints\CatalogPromotionScope;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CatalogPromotionScopeValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
        ScopeValidatorInterface $forTaxonsValidator,
        ScopeValidatorInterface $forVariantsValidator
    ): void {
        $this->beConstructedWith(
            [
                InForTaxonsScopeVariantChecker::TYPE,
                InForVariantsScopeVariantChecker::TYPE
            ],
            [
                InForTaxonsScopeVariantChecker::TYPE => $forTaxonsValidator,
                InForVariantsScopeVariantChecker::TYPE => $forVariantsValidator,
            ]
        );

        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_adds_violation_if_catalog_promotion_scope_has_invalid_type(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        CatalogPromotionScopeInterface $scope
    ): void {
        $scope->getType()->willReturn('wrong_type');

        $executionContext->buildViolation('sylius.catalog_promotion_scope.invalid_type')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('type')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($scope, new CatalogPromotionScope());
    }

    function it_calls_a_proper_validator_to_validate_the_configuration(
        ExecutionContextInterface $executionContext,
        CatalogPromotionScopeInterface $scope,
        ScopeValidatorInterface $forVariantsValidator
    ): void {
        $constraint = new CatalogPromotionScope();

        $scope->getType()->willReturn(InForVariantsScopeVariantChecker::TYPE);
        $scope->getConfiguration()->willReturn([]);

        $forVariantsValidator->validate([], $constraint, $executionContext)->shouldBeCalled();

        $this->validate($scope, $constraint);
    }
}
