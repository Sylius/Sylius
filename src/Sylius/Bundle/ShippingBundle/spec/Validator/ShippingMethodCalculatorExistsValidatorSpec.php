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
use Sylius\Bundle\ShippingBundle\Validator\Constraint\ShippingMethodCalculatorExists;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ShippingMethodCalculatorExistsValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
    ): void {
        $this->beConstructedWith(
            [
                'flat_rate' => 'sylius.form.shipping_calculator.flat_rate_configuration.label',
                'per_unit_rate' => 'sylius.form.shipping_calculator.per_unit_rate_configuration.label',
            ],
        );

        $this->initialize($executionContext);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_shipping_method_calculator_exists(
        Constraint $constraint,
        ShippingMethodRuleInterface $shippingMethodRule,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$shippingMethodRule, $constraint])
        ;
    }

    function it_adds_violation_to_wrong_shipping_method_calculator(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $executionContext->buildViolation((new ShippingMethodCalculatorExists())->invalidShippingCalculator)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate('wrong_calculator', new ShippingMethodCalculatorExists());
    }

    function it_does_not_add_violation_to_correct_shipping_method_calculator(ExecutionContextInterface $executionContext): void
    {
        $executionContext->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate('flat_rate', new ShippingMethodCalculatorExists());
    }
}
