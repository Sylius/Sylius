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
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionActionType;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionActionTypeValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context): void
    {
        $this->beConstructedWith(['action_one' => 'action_one', 'action_two' => 'action_two']);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_promotion_action_type(
        Constraint $constraint,
        PromotionActionInterface $promotionAction,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$promotionAction, $constraint])
        ;
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_promotion_action(): void
    {
        $this
            ->shouldThrow(UnexpectedValueException::class)
            ->during('validate', [new \stdClass(), new PromotionActionType()])
        ;
    }

    function it_adds_violation_if_promotion_action_has_invalid_type(
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        PromotionActionInterface $promotionAction,
    ): void {
        $promotionAction->getType()->willReturn('wrong_type');

        $context->buildViolation('sylius.promotion_action.invalid_type')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('type')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($promotionAction, new PromotionActionType());
    }
}
