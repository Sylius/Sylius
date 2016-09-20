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

use Symfony\Component\Validator\Constraint;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PromotionCoupon extends Constraint
{
    public $invalidMessage = 'sylius.promotion_coupon.is_invalid';
    public $expiredMessage = 'sylius.promotion_coupon.has_expired';
    public $usageLimitMessage = 'sylius.promotion_coupon.reached_usage_limit';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return [self::PROPERTY_CONSTRAINT];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_promotion_coupon_validator';
    }
}
