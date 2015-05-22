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

/**
 * Coupon interface.
 *
 * @author Myke Hines <myke@webhines.com>
 */
interface CouponInterface extends BaseCouponInterface
{
    /**
     * Get per customer usage limit.
     *
     * @return null|int
     */
    public function getPerCustomerUsageLimit();

    /**
     * Set per customer usage limit.
     *
     * @param int $perCustomerUsageLimit
     *
     * @return self
     */
    public function setPerCustomerUsageLimit($perCustomerUsageLimit = 0);
}
