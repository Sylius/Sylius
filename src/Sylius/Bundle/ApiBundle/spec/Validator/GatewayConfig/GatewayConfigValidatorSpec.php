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

namespace spec\Sylius\Bundle\ApiBundle\Validator\GatewayConfig;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Validator\Constraints\GatewayConfig;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class GatewayConfigValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $executionContext,
    ): void {
        $this->beConstructedWith(
            ['paypal_express_checkout' => 'sylius.payum_gateway_factory.paypal_express_checkout', 'stripe_checkout' => 'sylius.payum_gateway_factory.stripe_checkout'],
        );

        $this->initialize($executionContext);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_gateway_config(
        Constraint $constraint,
        GatewayConfigInterface $gatewayConfig,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$gatewayConfig, $constraint])
        ;
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_gateway_config(
        PaymentMethodInterface $paymentMethod,
    ): void {
        $this
            ->shouldThrow(UnexpectedValueException::class)
            ->during('validate', [$paymentMethod, new GatewayConfig()])
        ;
    }

    function it_validates_gateway_configuration_with_wrong_name(
        GatewayConfigInterface $gatewayConfig,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $gatewayConfig->getFactoryName()->willReturn('wrong_factory_name');

        $executionContext->buildViolation((new GatewayConfig())->invalidGatewayFactory)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('factoryName')->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->shouldBeCalled()->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($gatewayConfig, new GatewayConfig());
    }

    function it_validates_gateway_configuration(
        GatewayConfigInterface $gatewayConfig,
        ExecutionContextInterface $executionContext,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
    ): void {
        $gatewayConfig->getFactoryName()->willReturn('paypal_express_checkout');

        $executionContext->getValidator()->willReturn($validator);
        $validator->inContext($executionContext)->willReturn($contextualValidator);

        $contextualValidator->validate($gatewayConfig, null, ['Default', 'test_group', 'paypal_express_checkout'])->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($gatewayConfig, new GatewayConfig(['groups' => ['Default', 'test_group']]));
    }
}
