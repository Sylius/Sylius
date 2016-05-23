<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Generator;

use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * Coupon generator interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CouponGeneratorInterface
{
    /**
     * @param PromotionInterface $promotion
     * @param InstructionInterface $instruction
     * 
     * @return array of generated coupons with coupon code as a key
     */
    public function generate(PromotionInterface $promotion, InstructionInterface $instruction);
}
