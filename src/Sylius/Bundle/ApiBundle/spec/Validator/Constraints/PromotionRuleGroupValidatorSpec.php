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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PromotionRuleGroup;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PromotionRuleGroupValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context): void
    {
        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_promotion_rule_group(
        Constraint $constraint,
        PromotionRuleInterface $promotionRule,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$promotionRule, $constraint])
        ;
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_promotion_rule(
        PromotionInterface $promotion,
    ): void {
        $this
            ->shouldThrow(UnexpectedValueException::class)
            ->during('validate', [$promotion, new PromotionRuleGroup()])
        ;
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

        $contextualValidator->validate($promotionRule, null, ['Default', 'test_group', 'rule_two'])->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($promotionRule, new PromotionRuleGroup(['groups' => ['Default', 'test_group']]));
    }
}
