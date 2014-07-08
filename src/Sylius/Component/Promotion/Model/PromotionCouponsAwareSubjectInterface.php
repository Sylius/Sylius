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

/**
 * Coupons aware promotion subject interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionCouponsAwareSubjectInterface extends PromotionSubjectInterface
{
    /**
     * Gets associated promotion coupons.
     *
     * @return CouponInterface[]
     */
    public function getPromotionCoupons();
}
