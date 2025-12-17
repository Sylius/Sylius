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

namespace Tests\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentMethodEligibility;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentMethodEligibilityValidator;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ChosenPaymentMethodEligibilityValidatorTest extends TestCase
{
    private MockObject&PaymentRepositoryInterface $paymentRepository;

    private MockObject&PaymentMethodRepositoryInterface $paymentMethodRepository;

    private MockObject&PaymentMethodsResolverInterface $paymentMethodsResolver;

    private ExecutionContextInterface&MockObject $executionContext;

    private ChosenPaymentMethodEligibilityValidator $chosenPaymentMethodEligibilityValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->paymentMethodsResolver = $this->createMock(PaymentMethodsResolverInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->chosenPaymentMethodEligibilityValidator = new ChosenPaymentMethodEligibilityValidator($this->paymentRepository, $this->paymentMethodRepository, $this->paymentMethodsResolver);
        $this->chosenPaymentMethodEligibilityValidator->initialize($this->executionContext);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->chosenPaymentMethodEligibilityValidator);
    }

    public function testThrowsAnExceptionIfValueDoesNotExtendPaymentMethodCodeAwareInterface(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->chosenPaymentMethodEligibilityValidator->validate('', new ChosenPaymentMethodEligibility());
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfChosenPaymentMethodEligibility(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $invalidConstraint = $this->createMock(Constraint::class);

        $command = new ChoosePaymentMethod(
            orderTokenValue: 'ORDER_TOKEN',
            paymentMethodCode: 'code',
            paymentId: 123,
        );

        $this->chosenPaymentMethodEligibilityValidator->validate($command, $invalidConstraint);
    }

    public function testAddsViolationIfChosenPaymentMethodDoesNotMatchSupportedMethods(): void
    {
        /** @var PaymentMethodInterface|MockObject $firstPaymentMethodMock */
        $firstPaymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        /** @var PaymentMethodInterface|MockObject $secondPaymentMethodMock */
        $secondPaymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        /** @var PaymentMethodInterface|MockObject $thirdPaymentMethodMock */
        $thirdPaymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        /** @var PaymentInterface|MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $command = new ChoosePaymentMethod(
            orderTokenValue: 'ORDER_TOKEN',
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            paymentId: 123,
        );
        $this->paymentMethodRepository->expects(self::once())->method('findOneBy')->with(['code' => 'PAYMENT_METHOD_CODE'])->willReturn($firstPaymentMethodMock);
        $firstPaymentMethodMock->expects(self::once())->method('getName')->willReturn('offline');
        $this->paymentRepository->expects(self::once())->method('find')->with('123')->willReturn($paymentMock);
        $this->paymentMethodsResolver->expects(self::once())->method('getSupportedMethods')->with($paymentMock)->willReturn([$secondPaymentMethodMock, $thirdPaymentMethodMock]);
        $this->executionContext->expects(self::once())->method('addViolation')->with('sylius.payment_method.not_available', ['%name%' => 'offline'])
        ;
        $this->chosenPaymentMethodEligibilityValidator->validate($command, new ChosenPaymentMethodEligibility());
    }

    public function testAddsViolationIfPaymentDoesNotExist(): void
    {
        /** @var PaymentMethodInterface|MockObject $paymentMethodMock */
        $paymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        $command = new ChoosePaymentMethod(
            orderTokenValue: 'ORDER_TOKEN',
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            paymentId: 123,
        );
        $this->paymentMethodRepository->expects(self::once())->method('findOneBy')->with(['code' => 'PAYMENT_METHOD_CODE'])->willReturn($paymentMethodMock);
        $this->paymentRepository->expects(self::once())->method('find')->with('123')->willReturn(null);
        $this->executionContext->expects(self::once())->method('addViolation')->with('sylius.payment.not_found')
        ;
        $this->chosenPaymentMethodEligibilityValidator->validate($command, new ChosenPaymentMethodEligibility());
    }

    public function testAddsViolationIfPaymentMethodDoesNotExist(): void
    {
        $command = new ChoosePaymentMethod(
            orderTokenValue: 'ORDER_TOKEN',
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            paymentId: 123,
        );
        $this->paymentMethodRepository->expects(self::once())->method('findOneBy')->with(['code' => 'PAYMENT_METHOD_CODE'])->willReturn(null);
        $this->executionContext->expects(self::once())->method('addViolation')->with('sylius.payment_method.not_exist', ['%code%' => 'PAYMENT_METHOD_CODE'])
        ;
        $this->chosenPaymentMethodEligibilityValidator->validate($command, new ChosenPaymentMethodEligibility());
    }

    public function testDoesNothingIfPaymentMethodIsEligible(): void
    {
        /** @var PaymentMethodInterface|MockObject $firstPaymentMethodMock */
        $firstPaymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        /** @var PaymentMethodInterface|MockObject $secondPaymentMethodMock */
        $secondPaymentMethodMock = $this->createMock(PaymentMethodInterface::class);
        /** @var PaymentInterface|MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $command = new ChoosePaymentMethod(
            orderTokenValue: 'ORDER_TOKEN',
            paymentMethodCode: 'PAYMENT_METHOD_CODE',
            paymentId: 123,
        );
        $this->paymentMethodRepository->expects(self::once())->method('findOneBy')->with(['code' => 'PAYMENT_METHOD_CODE'])->willReturn($secondPaymentMethodMock);
        $firstPaymentMethodMock->method('getName')->willReturn('offline');
        $this->paymentRepository->expects(self::once())->method('find')->with('123')->willReturn($paymentMock);
        $this->paymentMethodsResolver->expects(self::once())->method('getSupportedMethods')->with($paymentMock)->willReturn([$firstPaymentMethodMock, $secondPaymentMethodMock]);
        $this->executionContext->expects(self::never())->method('addViolation')->with('sylius.payment_method.not_exist', ['%code%' => 'PAYMENT_METHOD_CODE'])
        ;
        $this->chosenPaymentMethodEligibilityValidator->validate(
            $command,
            new ChosenPaymentMethodEligibility(),
        );
    }
}
