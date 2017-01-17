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
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CouponPossibleGenerationAmount extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.promotion_coupon_generator_instruction.possible_generation_amount';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_coupon_generation_amount_validator';
    }
}
