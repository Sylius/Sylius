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

namespace Tests\Sylius\Bundle\PromotionBundle\Validator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionNotCouponBased;
use Sylius\Bundle\PromotionBundle\Validator\PromotionNotCouponBasedValidator;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionNotCouponBasedValidatorTest extends TestCase
{
    /** @var ExecutionContextInterface&MockObject */
    private MockObject $context;

    private PromotionNotCouponBasedValidator $promotionNotCouponBasedValidator;

    /** @var PromotionCouponInterface&MockObject */
    private PromotionCouponInterface $coupon;

    /** @var PromotionInterface&MockObject */
    private PromotionInterface $promotion;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->promotionNotCouponBasedValidator = new PromotionNotCouponBasedValidator();
        $this->promotionNotCouponBasedValidator->initialize($this->context);
        $this->coupon = $this->createMock(PromotionCouponInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
    }

    public function testDoesNothingWhenValueIsNull(): void
    {
        $this->context->expects(self::never())->method('buildViolation');

        $this->promotionNotCouponBasedValidator->validate(null, new PromotionNotCouponBased());
    }

    public function testThrowsAnExceptionWhenConstraintIsNotPromotionNotCouponBased(): void
    {
        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        $this->context->expects(self::never())->method('buildViolation');

        self::expectException(UnexpectedTypeException::class);

        $this->promotionNotCouponBasedValidator->validate($this->coupon, $constraint);
    }

    public function testThrowsAnExceptionWhenValueIsNotACoupon(): void
    {
        $this->context->expects(self::never())->method('buildViolation');

        self::expectException(UnexpectedValueException::class);

        $this->promotionNotCouponBasedValidator->validate(new \stdClass(), new PromotionNotCouponBased());
    }

    public function testDoesNothingWhenCouponHasNoPromotion(): void
    {
        $this->context->expects(self::never())->method('buildViolation');

        $this->coupon->expects(self::once())->method('getPromotion')->willReturn(null);

        $this->promotionNotCouponBasedValidator->validate($this->coupon, new PromotionNotCouponBased());
    }

    public function testDoesNothingWhenCouponHasPromotionAndItsCouponBased(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $this->promotion->expects(self::once())->method('isCouponBased')->willReturn(true);

        $this->coupon->expects(self::once())->method('getPromotion')->willReturn($this->promotion);

        $this->promotionNotCouponBasedValidator->validate($this->coupon, new PromotionNotCouponBased());
    }

    public function testAddsViolationWhenCouponHasPromotionButItsNotCouponBased(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $violationBuilder */
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $constraint = new PromotionNotCouponBased();
        $this->context->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())
            ->method('atPath')
            ->with('promotion')
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())->method('addViolation');

        $this->promotion->expects(self::once())->method('isCouponBased')->willReturn(false);

        $this->coupon->expects(self::once())->method('getPromotion')->willReturn($this->promotion);

        $this->promotionNotCouponBasedValidator->validate($this->coupon, $constraint);
    }
}
