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

use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionNotCouponBased;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class PromotionNotCouponBasedValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        /** @var PromotionNotCouponBased $constraint */
        Assert::isInstanceOf($constraint, PromotionNotCouponBased::class);

        /** @var PromotionCouponInterface $value */
        Assert::isInstanceOf($value, PromotionCouponInterface::class);

        $promotion = $value->getPromotion();
        if (null === $promotion) {
            return;
        }

        if (!$promotion->isCouponBased()) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('promotion')
                ->addViolation()
            ;
        }
    }
}
