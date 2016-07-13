<?php

/*
 * This file is a part of the Sylius package.
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
     * @var int
     */
    protected $perCustomerUsageLimit = 0;

    /**
     * {@inheritdoc}
     */
    public function getPerCustomerUsageLimit()
    {
        return $this->perCustomerUsageLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function setPerCustomerUsageLimit($perCustomerUsageLimit = 0)
    {
        $this->perCustomerUsageLimit = $perCustomerUsageLimit;
    }
}
