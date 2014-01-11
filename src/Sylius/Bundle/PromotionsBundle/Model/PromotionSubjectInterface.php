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
}
