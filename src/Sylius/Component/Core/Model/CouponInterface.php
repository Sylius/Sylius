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

use Sylius\Component\Promotion\Model\CouponInterface as BaseCouponInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

/**
 * Sylius core coupon model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface CouponInterface extends BaseCouponInterface
{
    public function getOrder();
    public function setOrder(BaseOrderInterface $order);
}
