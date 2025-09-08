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
use Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PromotionCouponEligibility;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PromotionCouponEligibilityValidator;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionCouponEligibilityValidatorTest extends TestCase
{
    private MockObject&PromotionCouponRepositoryInterface $promotionCouponRepository;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private AppliedCouponEligibilityCheckerInterface&MockObject $appliedCouponEligibilityChecker;

    private PromotionCouponEligibilityValidator $promotionCouponEligibilityValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->promotionCouponRepository = $this->createMock(PromotionCouponRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->appliedCouponEligibilityChecker = $this->createMock(AppliedCouponEligibilityCheckerInterface::class);
        $this->promotionCouponEligibilityValidator = new PromotionCouponEligibilityValidator($this->promotionCouponRepository, $this->orderRepository, $this->appliedCouponEligibilityChecker);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->promotionCouponEligibilityValidator);
    }

    public function testThrowsAnExceptionIfConstraintIsNotOfExpectedType(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->promotionCouponEligibilityValidator->validate('', new NotNull());
    }

    public function testDoesNotAddViolationIfPromotionCouponIsEligible(): void
    {
        /** @var PromotionCouponInterface|MockObject $promotionCouponMock */
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->promotionCouponEligibilityValidator->initialize($executionContextMock);
        $constraint = new PromotionCouponEligibility();
        $value = new UpdateCart(couponCode: 'couponCode', orderTokenValue: 'token');
        $this->promotionCouponRepository->expects(self::once())->method('findOneBy')->with(['code' => 'couponCode'])->willReturn($promotionCouponMock);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('token')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('setPromotionCoupon')->with($promotionCouponMock);
        $this->appliedCouponEligibilityChecker->expects(self::once())->method('isEligible')->with($promotionCouponMock, $cartMock)->willReturn(true);
        $executionContextMock->expects(self::never())->method('buildViolation');
        $this->promotionCouponEligibilityValidator->validate($value, $constraint);
    }

    public function testAddsViolationIfPromotionCouponIsNotEligible(): void
    {
        /** @var PromotionCouponInterface|MockObject $promotionCouponMock */
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ConstraintViolationBuilderInterface|MockObject $constraintViolationBuilderMock */
        $constraintViolationBuilderMock = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->promotionCouponEligibilityValidator->initialize($executionContextMock);
        $constraint = new PromotionCouponEligibility();
        $constraint->message = 'message';
        $value = new UpdateCart(couponCode: 'couponCode', orderTokenValue: 'token');
        $this->promotionCouponRepository->expects(self::once())->method('findOneBy')->with(['code' => 'couponCode'])->willReturn($promotionCouponMock);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('token')->willReturn($cartMock);
        $cartMock->expects(self::once())->method('setPromotionCoupon')->with($promotionCouponMock);
        $this->appliedCouponEligibilityChecker->expects(self::once())->method('isEligible')->with($promotionCouponMock, $cartMock)->willReturn(false);
        $executionContextMock->expects(self::once())->method('buildViolation')->with($constraint->message)->willReturn($constraintViolationBuilderMock);
        $constraintViolationBuilderMock->expects(self::once())->method('atPath')->with('couponCode')->willReturn($constraintViolationBuilderMock);
        $constraintViolationBuilderMock->expects(self::once())->method('addViolation');
        $this->promotionCouponEligibilityValidator->validate($value, $constraint);
    }

    public function testAddsViolationIfPromotionCouponIsNotInstanceOfPromotionCouponInterface(): void
    {
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ConstraintViolationBuilderInterface|MockObject $constraintViolationBuilderMock */
        $constraintViolationBuilderMock = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->promotionCouponEligibilityValidator->initialize($executionContextMock);
        $constraint = new PromotionCouponEligibility();
        $constraint->message = 'message';
        $value = new UpdateCart(couponCode: 'couponCode', orderTokenValue: 'token');
        $this->promotionCouponRepository->expects(self::once())->method('findOneBy')->with(['code' => 'couponCode'])->willReturn(null);
        $executionContextMock->expects(self::once())->method('buildViolation')->with($constraint->message)->willReturn($constraintViolationBuilderMock);
        $constraintViolationBuilderMock->expects(self::once())->method('atPath')->with('couponCode')->willReturn($constraintViolationBuilderMock);
        $constraintViolationBuilderMock->expects(self::once())->method('addViolation');
        $this->orderRepository->expects(self::never())->method('findCartByTokenValue')->with('token');
        $this->promotionCouponEligibilityValidator->validate($value, $constraint);
    }
}
