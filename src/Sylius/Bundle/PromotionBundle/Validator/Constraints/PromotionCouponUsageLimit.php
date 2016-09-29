<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Validator\Constraints;

use Sylius\Bundle\PromotionBundle\Validator\PromotionCouponUsageLimitValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PromotionCouponUsageLimit extends Constraint
{
    public $message = 'sylius.promotion_coupon.reached_usage_limit';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return [self::PROPERTY_CONSTRAINT, self::CLASS_CONSTRAINT];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return PromotionCouponUsageLimitValidator::class;
    }
}
