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
use Prophecy\Argument;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CatalogPromotionActionValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
        ActionValidatorInterface $fixedDiscountValidator,
        ActionValidatorInterface $percentageDiscountValidator,
    ): void {
        $this->beConstructedWith(
            ['fixed_discount', 'percentage_discount'],
            [
                'fixed_discount' => $fixedDiscountValidator,
                'percentage_discount' => $percentageDiscountValidator,
            ],
        );

        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_does_nothing_when_passed_action_type_has_no_validators(
        ExecutionContextInterface $executionContext,
        CatalogPromotionActionInterface $action,
    ): void {
        $action->getType()->willReturn('custom');

        $executionContext->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate($action, new CatalogPromotionAction());
    }

    function it_calls_a_proper_validator_to_validate_the_configuration(
        ExecutionContextInterface $executionContext,
        CatalogPromotionActionInterface $action,
        ActionValidatorInterface $percentageDiscountValidator,
    ): void {
        $constraint = new CatalogPromotionAction();

        $action->getType()->willReturn('percentage_discount');
        $action->getConfiguration()->willReturn([]);

        $percentageDiscountValidator->validate([], $constraint, $executionContext)->shouldBeCalled();

        $this->validate($action, $constraint);
    }
}
