<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionCouponsAwareSubjectInterface extends PromotionSubjectInterface
{
    /**
     * @param CouponInterface $coupon
     */
    public function addPromotionCoupon(CouponInterface $coupon);

    /**
     * @param CouponInterface $coupon
     */
    public function removePromotionCoupon(CouponInterface $coupon);

    /**
     * @param CouponInterface $coupon
     */
    public function hasPromotionCoupon(CouponInterface $coupon);

    /**
     * @return Collection|CouponInterface[]
     */
    public function getPromotionCoupons();
}
