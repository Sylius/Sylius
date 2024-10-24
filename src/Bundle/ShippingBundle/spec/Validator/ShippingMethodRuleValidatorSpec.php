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

namespace spec\Sylius\Bundle\ShippingBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ShippingBundle\Validator\Constraint\ShippingMethodRule;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ShippingMethodRuleValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
    ): void {
        $this->beConstructedWith(
            [
                'total_weight_greater_than_or_equal' => 'sylius.form.shipping_method_rule.total_weight_greater_than_or_equal',
                'order_total_greater_than_or_equal' => 'sylius.form.shipping_method_rule.items_total_greater_than_or_equal',
                'different_rule' => 'sylius.form.shipping_method_rule.different_rule',
            ],
            [
                'total_weight_greater_than_or_equal' => ['sylius', 'total_weight'],
                'order_total_greater_than_or_equal' => ['sylius', 'order_total'],
            ],
        );

        $this->initialize($executionContext);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_shipping_method_rule(
        Constraint $constraint,
        ShippingMethodRuleInterface $shippingMethodRule,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$shippingMethodRule, $constraint])
        ;
    }

    function it_adds_violation_to_shipping_method_rule_with_wrong_type(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        ShippingMethodRuleInterface $shippingMethodRule,
    ): void {
        $shippingMethodRule->getType()->willReturn('wrong_rule');
        $executionContext->buildViolation((new ShippingMethodRule())->invalidType)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath(Argument::cetera())->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($shippingMethodRule, new ShippingMethodRule());
    }

    function it_calls_a_validator_with_group(
        ExecutionContextInterface $executionContext,
        ShippingMethodRuleInterface $shippingMethodRule,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
    ): void {
        $shippingMethodRule->getType()->willReturn('total_weight_greater_than_or_equal');
        $executionContext->getValidator()->willReturn($validator);
        $validator->inContext($executionContext)->willReturn($contextualValidator);

        $contextualValidator->validate($shippingMethodRule, null, ['sylius', 'total_weight'])->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($shippingMethodRule, new ShippingMethodRule(['groups' => ['sylius', 'total_weight']]));
    }

    function it_calls_validator_with_default_groups_if_none_assigned_to_shipping_method_rule(
        ExecutionContextInterface $executionContext,
        ShippingMethodRuleInterface $shippingMethodRule,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
    ): void {
        $shippingMethodRule->getType()->willReturn('different_rule');

        $executionContext->getValidator()->willReturn($validator);
        $validator->inContext($executionContext)->willReturn($contextualValidator);

        $contextualValidator->validate($shippingMethodRule, null, ['sylius'])->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($shippingMethodRule, new ShippingMethodRule(['groups' => ['sylius']]));
    }
}
