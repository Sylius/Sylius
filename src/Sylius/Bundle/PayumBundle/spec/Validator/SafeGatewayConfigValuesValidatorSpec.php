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

namespace spec\Sylius\Bundle\PayumBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\PayumBundle\Validator\Constraints\SafeGatewayConfigValues;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class SafeGatewayConfigValuesValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext): void
    {
        $this->initialize($executionContext);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_safe_gateway_config_values(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [[], $constraint])
        ;
    }

    function it_does_nothing_when_the_value_is_not_an_array(
        ExecutionContextInterface $executionContext,
    ): void {
        $executionContext->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate(null, new SafeGatewayConfigValues());
    }

    function it_does_not_add_a_violation_for_a_plain_scalar_configuration(
        ExecutionContextInterface $executionContext,
    ): void {
        $executionContext->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate(['sandbox' => true, 'publishable_key' => 'pk_test_123'], new SafeGatewayConfigValues());
    }

    function it_adds_a_violation_for_a_value_referencing_a_container_parameter(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $executionContext
            ->buildViolation((new SafeGatewayConfigValues())->forbiddenValueMessage)
            ->willReturn($constraintViolationBuilder)
        ;
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['leaked' => '%kernel.secret%'], new SafeGatewayConfigValues());
    }

    function it_adds_a_violation_for_a_value_referencing_a_service(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $executionContext
            ->buildViolation((new SafeGatewayConfigValues())->forbiddenValueMessage)
            ->willReturn($constraintViolationBuilder)
        ;
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['injected' => '@security.token_storage'], new SafeGatewayConfigValues());
    }

    function it_adds_a_violation_for_a_callable_value(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $executionContext
            ->buildViolation((new SafeGatewayConfigValues())->forbiddenValueMessage)
            ->willReturn($constraintViolationBuilder)
        ;
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['callback' => 'phpinfo'], new SafeGatewayConfigValues());
    }

    function it_adds_a_violation_for_a_callable_array_value(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $executionContext
            ->buildViolation((new SafeGatewayConfigValues())->forbiddenValueMessage)
            ->willReturn($constraintViolationBuilder)
        ;
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['callback' => [\DateTime::class, 'getLastErrors']], new SafeGatewayConfigValues());
    }

    function it_does_not_add_a_violation_for_a_non_callable_array_value(
        ExecutionContextInterface $executionContext,
    ): void {
        $executionContext->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate(['keys' => ['first', 'second']], new SafeGatewayConfigValues());
    }
}
