<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Model;

use Sylius\Promotion\Model\CouponInterface as BaseCouponInterface;

/**
 * @author Myke Hines <myke@webhines.com>
 */
interface CouponInterface extends BaseCouponInterface
{
    /**
     * @return null|int
     */
    public function getPerCustomerUsageLimit();

    /**
     * @param int $perCustomerUsageLimit
     */
    public function setPerCustomerUsageLimit($perCustomerUsageLimit = 0);
}
