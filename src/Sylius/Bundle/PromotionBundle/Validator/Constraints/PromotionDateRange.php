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
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PromotionDateRange extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.promotion.end_date_cannot_be_set_prior_start_date';

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
        return 'sylius_promotion_date_range_validator';
    }
}
