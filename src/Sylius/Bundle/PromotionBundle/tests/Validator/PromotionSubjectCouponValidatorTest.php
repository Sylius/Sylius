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
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionSubjectCoupon;
use Sylius\Bundle\PromotionBundle\Validator\PromotionSubjectCouponValidator;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponAwarePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionSubjectCouponValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private MockObject&PromotionEligibilityCheckerInterface $promotionEligibilityChecker;

    private PromotionSubjectCouponValidator $validator;

    private MockObject&PromotionCouponAwarePromotionSubjectInterface $subject;

    private MockObject&PromotionCouponInterface $coupon;

    private MockObject&PromotionInterface $promotion;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->promotionEligibilityChecker = $this->createMock(PromotionEligibilityCheckerInterface::class);
        $this->validator = new PromotionSubjectCouponValidator($this->promotionEligibilityChecker);
        $this->validator->initialize($this->context);
        $this->subject = $this->createMock(PromotionCouponAwarePromotionSubjectInterface::class);
        $this->coupon = $this->createMock(PromotionCouponInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
    }

    public function testDoesNothingWhenValueIsNotACouponAwareSubject(): void
    {
        $this->promotionEligibilityChecker->expects(self::never())->method('isEligible');
        $this->context->expects(self::never())->method('buildViolation');

        $this->validator->validate(new \stdClass(), new PromotionSubjectCoupon());
    }

    public function testDoesNothingWhenSubjectHasNoCoupon(): void
    {
        $this->subject->expects(self::once())->method('getPromotionCoupon')->willReturn(null);

        $this->promotionEligibilityChecker->expects(self::never())->method('isEligible');
        $this->context->expects(self::never())->method('buildViolation');

        $this->validator->validate($this->subject, new PromotionSubjectCoupon());
    }

    public function testDoesNotAddViolationWhenThePromotionIsEligible(): void
    {
        $this->subject->method('getPromotionCoupon')->willReturn($this->coupon);
        $this->coupon->method('getPromotion')->willReturn($this->promotion);

        $this->promotionEligibilityChecker->expects(self::once())
            ->method('isEligible')
            ->with($this->subject, $this->promotion)
            ->willReturn(true);

        $this->context->expects(self::never())->method('buildViolation');

        $this->validator->validate($this->subject, new PromotionSubjectCoupon());
    }

    public function testAddsViolationWhenThePromotionIsNotEligible(): void
    {
        $this->subject->method('getPromotionCoupon')->willReturn($this->coupon);
        $this->coupon->method('getPromotion')->willReturn($this->promotion);

        $this->promotionEligibilityChecker->expects(self::once())
            ->method('isEligible')
            ->with($this->subject, $this->promotion)
            ->willReturn(false);

        $constraint = new PromotionSubjectCoupon();

        /** @var ConstraintViolationBuilderInterface&MockObject $violationBuilder */
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->context->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())
            ->method('atPath')
            ->with('promotionCoupon')
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())->method('addViolation');

        $this->validator->validate($this->subject, $constraint);
    }
}
