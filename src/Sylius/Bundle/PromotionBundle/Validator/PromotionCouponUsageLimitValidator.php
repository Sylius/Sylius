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

use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionCouponUsageLimit;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PromotionCouponUsageLimitValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var PromotionCouponUsageLimit $constraint */
        Assert::isInstanceOf($constraint, PromotionCouponUsageLimit::class);

        if (!$value instanceof PromotionCouponInterface) {
            return;
        }

        if ($value->getUsageLimit() === null) {
            return;
        }

        if ($value->getUsageLimit() > $value->getUsed()) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}
