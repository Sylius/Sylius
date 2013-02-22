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

use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Coupon aware order model interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface CouponAwareOrderInterface extends OrderInterface
{
    public function getCoupon();
    public function setCoupon(CouponInterface $coupon);
}
