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
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionCouponEligibilityValidatorTest extends TestCase
{
    /** @var PromotionCouponRepositoryInterface<PromotionCouponInterface>&MockObject */
    private MockObject $promotionCouponRepositoryMock;

    /** @var OrderRepositoryInterface<OrderInterface>&MockObject */
    private MockObject $orderRepositoryMock;

    private AppliedCouponEligibilityCheckerInterface&MockObject $appliedCouponEligibilityCheckerMock;

    private MockObject&PromotionCouponEligibilityCheckerInterface $durationEligibilityCheckerMock;

    private ExecutionContextInterface&MockObject $executionContextMock;

    private PromotionCouponEligibilityValidator $validator;

    protected function setUp(): void
    {
        $this->promotionCouponRepositoryMock = $this->createMock(PromotionCouponRepositoryInterface::class);
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->appliedCouponEligibilityCheckerMock = $this->createMock(AppliedCouponEligibilityCheckerInterface::class);
        $this->durationEligibilityCheckerMock = $this->createMock(PromotionCouponEligibilityCheckerInterface::class);
        $this->executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new PromotionCouponEligibilityValidator(
            $this->promotionCouponRepositoryMock,
            $this->orderRepositoryMock,
            $this->appliedCouponEligibilityCheckerMock,
            $this->durationEligibilityCheckerMock,
        );
        $this->validator->initialize($this->executionContextMock);
    }

    public function test_it_is_a_constraint_validator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->validator);
    }

    public function test_it_throws_an_exception_if_constraint_is_not_of_expected_type(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $this->validator->validate(new UpdateCart(orderTokenValue: 'token'), new NotNull());
    }

    public function test_it_throws_an_exception_if_cart_does_not_exist(): void
    {
        $this->promotionCouponRepositoryMock
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'couponCode'])
            ->willReturn($this->createMock(PromotionCouponInterface::class));

        $this->orderRepositoryMock
            ->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('token')
            ->willReturn(null);

        self::expectException(\InvalidArgumentException::class);

        $this->validator->validate(
            new UpdateCart(orderTokenValue: 'token', couponCode: 'couponCode'),
            new PromotionCouponEligibility(),
        );
    }

    public function test_it_does_not_add_violation_if_coupon_code_is_not_provided(): void
    {
        $this->promotionCouponRepositoryMock->expects(self::never())->method('findOneBy');
        $this->orderRepositoryMock->expects(self::never())->method('findCartByTokenValue');
        $this->executionContextMock->expects(self::never())->method('buildViolation');

        $this->validator->validate(new UpdateCart(orderTokenValue: 'token'), new PromotionCouponEligibility());
    }

    public function test_it_adds_violation_if_promotion_coupon_does_not_exist(): void
    {
        $constraint = new PromotionCouponEligibility();

        $this->promotionCouponRepositoryMock
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'couponCode'])
            ->willReturn(null);

        $this->orderRepositoryMock->expects(self::never())->method('findCartByTokenValue');
        $this->appliedCouponEligibilityCheckerMock->expects(self::never())->method('isEligible');

        $this->expectViolation($constraint->invalidMessage, PromotionCouponEligibility::PROMOTION_COUPON_INVALID_ERROR);

        $this->validator->validate(new UpdateCart(orderTokenValue: 'token', couponCode: 'couponCode'), $constraint);
    }

    public function test_it_adds_violation_if_promotion_coupon_has_expired(): void
    {
        $constraint = new PromotionCouponEligibility();
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        $cartMock = $this->createMock(OrderInterface::class);

        $this->promotionCouponRepositoryMock
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'couponCode'])
            ->willReturn($promotionCouponMock);

        $this->orderRepositoryMock
            ->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('token')
            ->willReturn($cartMock);

        $cartMock->expects(self::once())->method('setPromotionCoupon')->with($promotionCouponMock);

        $this->durationEligibilityCheckerMock
            ->expects(self::once())
            ->method('isEligible')
            ->with($cartMock, $promotionCouponMock)
            ->willReturn(false);

        $this->appliedCouponEligibilityCheckerMock->expects(self::never())->method('isEligible');

        $this->expectViolation($constraint->expiredMessage, PromotionCouponEligibility::PROMOTION_COUPON_EXPIRED_ERROR);

        $this->validator->validate(new UpdateCart(orderTokenValue: 'token', couponCode: 'couponCode'), $constraint);
    }

    public function test_it_adds_violation_if_promotion_of_the_promotion_coupon_is_not_eligible(): void
    {
        $constraint = new PromotionCouponEligibility();
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        $cartMock = $this->createMock(OrderInterface::class);

        $this->promotionCouponRepositoryMock
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'couponCode'])
            ->willReturn($promotionCouponMock);

        $this->orderRepositoryMock
            ->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('token')
            ->willReturn($cartMock);

        $cartMock->expects(self::once())->method('setPromotionCoupon')->with($promotionCouponMock);

        $this->durationEligibilityCheckerMock
            ->expects(self::once())
            ->method('isEligible')
            ->with($cartMock, $promotionCouponMock)
            ->willReturn(true);

        $this->appliedCouponEligibilityCheckerMock
            ->expects(self::once())
            ->method('isEligible')
            ->with($promotionCouponMock, $cartMock)
            ->willReturn(false);

        $this->expectViolation($constraint->ineligibleMessage, PromotionCouponEligibility::PROMOTION_COUPON_INELIGIBLE_ERROR);

        $this->validator->validate(new UpdateCart(orderTokenValue: 'token', couponCode: 'couponCode'), $constraint);
    }

    public function test_it_does_not_add_violation_if_promotion_coupon_is_eligible(): void
    {
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        $cartMock = $this->createMock(OrderInterface::class);

        $this->promotionCouponRepositoryMock
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'couponCode'])
            ->willReturn($promotionCouponMock);

        $this->orderRepositoryMock
            ->expects(self::once())
            ->method('findCartByTokenValue')
            ->with('token')
            ->willReturn($cartMock);

        $cartMock->expects(self::once())->method('setPromotionCoupon')->with($promotionCouponMock);

        $this->durationEligibilityCheckerMock
            ->expects(self::once())
            ->method('isEligible')
            ->with($cartMock, $promotionCouponMock)
            ->willReturn(true);

        $this->appliedCouponEligibilityCheckerMock
            ->expects(self::once())
            ->method('isEligible')
            ->with($promotionCouponMock, $cartMock)
            ->willReturn(true);

        $this->executionContextMock->expects(self::never())->method('buildViolation');

        $this->validator->validate(
            new UpdateCart(orderTokenValue: 'token', couponCode: 'couponCode'),
            new PromotionCouponEligibility(),
        );
    }

    private function expectViolation(string $message, string $code): void
    {
        $constraintViolationBuilderMock = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->executionContextMock
            ->expects(self::once())
            ->method('buildViolation')
            ->with($message)
            ->willReturn($constraintViolationBuilderMock);

        $constraintViolationBuilderMock->expects(self::once())->method('atPath')->with('couponCode')->willReturnSelf();
        $constraintViolationBuilderMock->expects(self::once())->method('setCode')->with($code)->willReturnSelf();
        $constraintViolationBuilderMock->expects(self::once())->method('addViolation');
    }
}
