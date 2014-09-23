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

class Coupon extends BaseCoupon implements CouponInterface
{
    /**
     * The per user usage limit
     *
     * @var int
     */
    protected $perUserUsageLimit = 0;

    /**
     * {@inheritdoc}
     */
    public function getPerUserUsageLimit()
    {
        return $this->perUserUsageLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function setPerUserUsageLimit($perUserUsageLimit = 0)
    {
        $this->perUserUsageLimit = $perUserUsageLimit;

        return $this;
    }
}
