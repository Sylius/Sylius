<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Validator;

use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionSubjectCoupon;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponAwarePromotionSubjectInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PromotionSubjectCouponValidator extends ConstraintValidator
{
    /**
     * @var PromotionEligibilityCheckerInterface
     */
    private $promotionEligibilityChecker;

    /**
     * @param PromotionEligibilityCheckerInterface $promotionEligibilityChecker
     */
    public function __construct(PromotionEligibilityCheckerInterface $promotionEligibilityChecker)
    {
        $this->promotionEligibilityChecker = $promotionEligibilityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
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
