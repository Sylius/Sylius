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
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionActionGroup;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PromotionActionGroupValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context): void
    {
        $this->beConstructedWith(['action_two' => ['Default', 'action_two']]);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_promotion_action_group(
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
            ->during('validate', [new \stdClass(), new PromotionActionGroup()])
        ;
    }

    function it_calls_a_validator_with_group(
        ExecutionContextInterface $context,
        PromotionActionInterface $promotionAction,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
    ): void {
        $promotionAction->getType()->willReturn('action_two');

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator->validate($promotionAction, null, ['Default', 'action_two'])->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($promotionAction, new PromotionActionGroup(['groups' => ['Default', 'test_group']]));
    }

    function it_calls_validator_with_default_groups_if_none_provided_for_promotion_action_type(
        ExecutionContextInterface $context,
        PromotionActionInterface $promotionAction,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
    ): void {
        $promotionAction->getType()->willReturn('action_one');

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator->validate($promotionAction, null, ['Default', 'test_group'])->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($promotionAction, new PromotionActionGroup(['groups' => ['Default', 'test_group']]));
    }
}
