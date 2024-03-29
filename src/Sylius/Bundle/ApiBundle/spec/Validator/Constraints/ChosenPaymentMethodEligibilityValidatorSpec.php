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
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentMethodEligibility;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ChosenPaymentMethodEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->beConstructedWith($paymentRepository, $paymentMethodRepository, $paymentMethodsResolver);

        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_does_not_extend_payment_method_code_aware_interface(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new ChosenPaymentMethodEligibility()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_chosen_payment_method_eligibility(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new ChoosePaymentMethod('code'), new class() extends Constraint {
            }])
        ;
    }

    function it_adds_violation_if_chosen_payment_method_does_not_match_supported_methods(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        ExecutionContextInterface $executionContext,
        PaymentMethodInterface $firstPaymentMethod,
        PaymentMethodInterface $secondPaymentMethod,
        PaymentMethodInterface $thirdPaymentMethod,
        PaymentInterface $payment,
    ): void {
        $command = new ChoosePaymentMethod('PAYMENT_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $paymentMethodRepository->findOneBy(['code' => 'PAYMENT_METHOD_CODE'])->willReturn($firstPaymentMethod);
        $firstPaymentMethod->getName()->willReturn('offline');

        $paymentRepository->find('123')->willReturn($payment);

        $paymentMethodsResolver->getSupportedMethods($payment)->willReturn([$secondPaymentMethod, $thirdPaymentMethod]);

        $executionContext
            ->addViolation('sylius.payment_method.not_available', ['%name%' => 'offline'])
            ->shouldBeCalled()
        ;

        $this->validate($command, new ChosenPaymentMethodEligibility());
    }

    function it_adds_violation_if_payment_does_not_exist(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        ExecutionContextInterface $executionContext,
    ): void {
        $command = new ChoosePaymentMethod('PAYMENT_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $paymentMethodRepository->findOneBy(['code' => 'PAYMENT_METHOD_CODE'])->willReturn($paymentMethod);

        $paymentRepository->find('123')->willReturn(null);

        $executionContext
            ->addViolation('sylius.payment.not_found')
            ->shouldBeCalled()
        ;

        $this->validate($command, new ChosenPaymentMethodEligibility());
    }

    function it_adds_violation_if_payment_method_does_not_exist(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $command = new ChoosePaymentMethod('PAYMENT_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $paymentMethodRepository->findOneBy(['code' => 'PAYMENT_METHOD_CODE'])->willReturn(null);

        $executionContext
            ->addViolation('sylius.payment_method.not_exist', ['%code%' => 'PAYMENT_METHOD_CODE'])
            ->shouldBeCalled()
        ;

        $this->validate($command, new ChosenPaymentMethodEligibility());
    }

    function it_does_nothing_if_payment_method_is_eligible(
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        ExecutionContextInterface $executionContext,
        PaymentMethodInterface $firstPaymentMethod,
        PaymentMethodInterface $secondPaymentMethod,
        PaymentInterface $payment,
    ): void {
        $command = new ChoosePaymentMethod('PAYMENT_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $paymentMethodRepository->findOneBy(['code' => 'PAYMENT_METHOD_CODE'])->willReturn($secondPaymentMethod);

        $firstPaymentMethod->getName()->willReturn('offline');

        $paymentRepository->find('123')->willReturn($payment);

        $paymentMethodsResolver->getSupportedMethods($payment)->willReturn([$firstPaymentMethod, $secondPaymentMethod]);

        $executionContext
            ->addViolation('sylius.payment_method.not_exist', ['%code%' => 'PAYMENT_METHOD_CODE'])
            ->shouldNotBeCalled()
        ;

        $this->validate(
            $command,
            new ChosenPaymentMethodEligibility(),
        );
    }
}
