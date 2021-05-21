<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Command\PaymentMethodCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentMethodEligibility;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ChosenPaymentMethodEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $this->beConstructedWith($paymentMethodRepository);

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
            ->during('validate', ['', new ChosenPaymentMethodEligibility()]);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_chosen_payment_method_eligibility(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new ChoosePaymentMethod('code'), new class() extends Constraint {}])
        ;
    }

    function it_adds_violation_if_payment_does_not_exist(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $paymentMethodRepository->findOneBy(['code' => 'payment_method_code'])->willReturn(null);

        $executionContext
            ->addViolation('sylius.payment_method.not_exist', ['%paymentMethodCode%' => 'payment_method_code'])
            ->shouldBeCalled();

        $this->validate(
            new ChoosePaymentMethod('payment_method_code'),
            new ChosenPaymentMethodEligibility()
        );
    }

    function it_does_nothing_if_payment_method_is_eligible(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        ExecutionContextInterface $executionContext
    ): void {
        $paymentMethodRepository->findOneBy(['code' => 'payment_method_code'])->willReturn($paymentMethod);

        $executionContext
            ->addViolation('sylius.payment_method.not_exist', ['%paymentMethodCode%' => 'payment_method_code'])
            ->shouldNotBeCalled();

        $this->validate(
            new ChoosePaymentMethod('payment_method_code'),
            new ChosenPaymentMethodEligibility()
        );
    }
}
