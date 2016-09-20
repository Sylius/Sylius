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

use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionCoupon;
use Sylius\Component\Promotion\Model\Coupon;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PromotionCouponValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var PromotionCoupon $constraint */
        Assert::isInstanceOf($constraint, PromotionCoupon::class);

        if (!$value instanceof Coupon) {
            $this->context->addViolation($constraint->invalidMessage);

            return;
        }

        if ($value->getExpiresAt() !== null && $value->getExpiresAt() < new \DateTime()) {
            $this->context->addViolation($constraint->expiredMessage);

            return;
        }

        if ($value->getPromotion()->getEndsAt() !== null && $value->getPromotion()->getEndsAt() < new \DateTime()) {
            $this->context->addViolation($constraint->expiredMessage);

            return;
        }

        if ($value->getUsageLimit() !== null && $value->getUsageLimit() <= $value->getUsed()) {
            $this->context->addViolation($constraint->usageLimitMessage);

            return;
        }
    }
}
