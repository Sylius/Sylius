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
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionRule;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionRuleValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context): void
    {
        $this->beConstructedWith(
            ['rule_one' => 'rule_one', 'rule_two' => 'rule_two'],
            ['rule_two' => ['Default', 'rule_two']],
        );

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_promotion_rule(
        Constraint $constraint,
        PromotionRuleInterface $promotionRule,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$promotionRule, $constraint])
        ;
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_array(): void
    {
        $this
            ->shouldThrow(UnexpectedValueException::class)
            ->during('validate', [new \stdClass(), new PromotionRule()])
        ;
    }

    function it_adds_violation_if_promotion_rule_has_invalid_type(
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        PromotionRuleInterface $promotionRule,
    ): void {
        $promotionRule->getType()->willReturn('wrong_type');

        $context->buildViolation('sylius.promotion_rule.invalid_type')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('type')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($promotionRule, new PromotionRule());
    }

    function it_calls_a_validator_with_group(
        ExecutionContextInterface $context,
        PromotionRuleInterface $promotionRule,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
    ): void {
        $promotionRule->getType()->willReturn('rule_two');

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator->validate($promotionRule, null, ['Default', 'rule_two'])->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($promotionRule, new PromotionRule(['groups' => ['Default', 'test_group']]));
    }

    function it_calls_validator_with_default_groups_if_none_provided_for_promotion_action_type(
        ExecutionContextInterface $context,
        PromotionRuleInterface $promotionRule,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
    ): void {
        $promotionRule->getType()->willReturn('rule_one');

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator->validate($promotionRule, null, ['Default', 'test_group'])->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($promotionRule, new PromotionRule(['groups' => ['Default', 'test_group']]));
    }
}
