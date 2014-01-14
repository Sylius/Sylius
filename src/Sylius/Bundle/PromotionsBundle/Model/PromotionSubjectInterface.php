<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Model;

use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;

/**
 * Promotion subject interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface PromotionSubjectInterface
{
    /**
     * Get associated coupon
     *
     * @return null|CouponInterface
     */
    public function getPromotionCoupon();

    /**
     * Get number of promotion subjects
     *
     * @return integer
     */
    public function getPromotionSubjectItemCount();

    /**
     * Get total of promotion subjects
     *
     * @return integer
     */
    public function getPromotionSubjectItemTotal();

    /**
     * Has Promotion
     *
     * @param PromotionInterface $promotion
     * @return bool
     */
    public function hasPromotion(PromotionInterface $promotion);

    /**
     * Add Promotion
     *
     * @param PromotionInterface $promotion
     * @return self
     */
    public function addPromotion(PromotionInterface $promotion);

    /**
     * Remove Promotion
     *
     * @param PromotionInterface $promotion
     * @return self
     */
    public function removePromotion(PromotionInterface $promotion);

    /**
     * Get Promotions
     *
     * @return PromotionInterface[]
     */
    public function getPromotions();
}
