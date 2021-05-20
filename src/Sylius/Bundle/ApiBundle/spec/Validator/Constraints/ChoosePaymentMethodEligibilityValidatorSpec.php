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
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChoosePaymentMethodEligibility;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ChoosePaymentMethodEligibilityValidatorSpec extends ObjectBehavior
{
    function let(PaymentMethodRepositoryInterface $paymentMethodRepository): void
    {
        $this->beConstructedWith($paymentMethodRepository);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_constraint_does_not_extend_payment_method_code_aware_interface(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {
            }]);
    }

    function it_throws_an_exception_if_constraint_does_not_type_of_choose_payment_method_eligibility(): void
    {
        $constraint = new class() extends Constraint implements PaymentMethodCodeAwareInterface {
            private $paymentMethodCode;

            function getPaymentMethodCode(): ?string
            {
                return 'abc';
            }

            function setPaymentMethodCode(?string $paymentMethodCode): void
            {
                $this->paymentMethodCode = $paymentMethodCode;
            }
        };

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', $constraint]);
    }

    function it_adds_violation_if_payment_does_not_exist(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $constraint = new ChoosePaymentMethodEligibility();

        $value = new ChoosePaymentMethod('code');

        $paymentMethodRepository->findOneBy(['code' => $value->getPaymentMethodCode()])->willReturn(null);

        $executionContext
            ->addViolation(
                'sylius.payment_method.not_exist',
                ['%paymentMethodCode%' => 'code']
            )
            ->shouldBeCalled();

        $this->validate($value, $constraint);
    }

    function it_does_nothing_if_payment_method_is_eligible(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $constraint = new ChoosePaymentMethodEligibility();

        $value = new ChoosePaymentMethod('code');

        $paymentMethodRepository->findOneBy(['code' => $value->getPaymentMethodCode()])->willReturn($paymentMethod);

        $executionContext
            ->addViolation(
                'sylius.payment_method.not_exist',
                ['%paymentMethodCode%' => 'code']
            )
            ->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }
}
