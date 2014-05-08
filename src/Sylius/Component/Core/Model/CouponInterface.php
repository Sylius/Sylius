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
 * Coupon model.
 *
 * @author Myke Hines <myke@webhines.com>
 */
interface CouponInterface extends BaseCouponInterface
{
    /**
     * @return null|int
     */
    public function getPerUserUsageLimit();

    /**
     * @param null|int $perUserUsageLimit
     */
    public function setPerUserUsageLimit($perUserUsageLimit);
}
