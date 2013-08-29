<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Generator;

use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;

/**
 * Coupon generator interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CouponGeneratorInterface
{
    /**
     * Generate coupons for the promotion based on the instruction.
     *
     * @param PromotionInterface $promotion
     * @param Instruction        $instruction
     */
    public function generate(PromotionInterface $promotion, Instruction $instruction);

    /**
     * Generate unique code.
     *
     * @return string
     */
    public function generateUniqueCode();
}
