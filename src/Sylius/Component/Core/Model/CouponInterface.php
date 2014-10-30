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
     * Get per user usage limit.
     *
     * @return null|int
     */
    public function getPerUserUsageLimit();

    /**
     * Set per user usage limit.
     *
     * @param int $perUserUsageLimit
     *
     * @return self
     */
    public function setPerUserUsageLimit($perUserUsageLimit = 0);
}
