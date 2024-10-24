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

namespace Sylius\Bundle\PromotionBundle\Validator;

use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionSubjectCoupon;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponAwarePromotionSubjectInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class PromotionSubjectCouponValidator extends ConstraintValidator
{
    public function __construct(private PromotionEligibilityCheckerInterface $promotionEligibilityChecker)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var PromotionSubjectCoupon $constraint */
        Assert::isInstanceOf($constraint, PromotionSubjectCoupon::class);

        if (!$value instanceof PromotionCouponAwarePromotionSubjectInterface) {
            return;
        }

        $promotionCoupon = $value->getPromotionCoupon();
        if ($promotionCoupon === null) {
            return;
        }

        if ($this->promotionEligibilityChecker->isEligible($value, $promotionCoupon->getPromotion())) {
            return;
        }

        $this->context->buildViolation($constraint->message)->atPath('promotionCoupon')->addViolation();
    }
}
