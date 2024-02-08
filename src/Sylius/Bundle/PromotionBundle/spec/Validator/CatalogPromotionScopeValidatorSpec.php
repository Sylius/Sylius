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

namespace spec\Sylius\Bundle\PromotionBundle\Validator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScope;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CatalogPromotionScopeValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
        ScopeValidatorInterface $forTaxonsValidator,
        ScopeValidatorInterface $forVariantsValidator,
    ): void {
        $this->beConstructedWith(
            [
                'for_taxons',
                'for_variants',
            ],
            [
                'for_taxons' => $forTaxonsValidator,
                'for_variants' => $forVariantsValidator,
            ],
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
        CatalogPromotionScopeInterface $scope,
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
        ScopeValidatorInterface $forVariantsValidator,
    ): void {
        $constraint = new CatalogPromotionScope();

        $scope->getType()->willReturn('for_variants');
        $scope->getConfiguration()->willReturn([]);

        $forVariantsValidator->validate([], $constraint, $executionContext)->shouldBeCalled();

        $this->validate($scope, $constraint);
    }
}
