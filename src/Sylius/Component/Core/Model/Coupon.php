<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Promotion\Model\Coupon as BaseCoupon;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

/**
 * Sylius core coupon model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Coupon extends BaseCoupon implements CouponInterface
{
    protected $order;

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(BaseOrderInterface $order)
    {
        $this->order = $order;

        return $this;
    }
}
