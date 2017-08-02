<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class PromotionSubjectCoupon extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.promotion_coupon.is_invalid';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return [self::CLASS_CONSTRAINT];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_promotion_subject_validator';
    }
}
