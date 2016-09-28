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

use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionCouponExpiry;
use Sylius\Component\Promotion\Model\CouponInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PromotionCouponExpiryValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var PromotionCouponExpiry$constraint */
        Assert::isInstanceOf($constraint, PromotionCouponExpiry::class);

        if (!$value instanceof CouponInterface) {
            return;
        }

        if ($value->getExpiresAt() !== null && $value->getExpiresAt() < new \DateTime()) {
            $this->context->addViolation($constraint->message);

            return;
        }

        if ($value->getPromotion()->getEndsAt() !== null && $value->getPromotion()->getEndsAt() < new \DateTime()) {
            $this->context->addViolation($constraint->message);

            return;
        }
    }
}
