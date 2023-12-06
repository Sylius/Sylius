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
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Bundle\PayumBundle\Validator\Constraints\GatewayFactoryExists;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class GatewayFactoryExistsValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
    ): void {
        $this->beConstructedWith(
            ['paypal_express_checkout' => 'sylius.payum_gateway_factory.paypal_express_checkout', 'stripe_checkout' => 'sylius.payum_gateway_factory.stripe_checkout'],
        );

        $this->initialize($executionContext);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_gateway_factory_exists(
        Constraint $constraint,
        GatewayConfigInterface $gatewayConfig,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$gatewayConfig, $constraint])
        ;
    }

    function it_adds_violation_to_gateway_configuration_with_wrong_name(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $executionContext->buildViolation((new GatewayFactoryExists())->invalidGatewayFactory)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate('wrong_factory', new GatewayFactoryExists());
    }

    function it_does_not_add_violation_to_gateway_configuration_with_correct_name(
        ExecutionContextInterface $executionContext,
    ): void {
        $executionContext->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate('paypal_express_checkout', new GatewayFactoryExists());
    }
}
