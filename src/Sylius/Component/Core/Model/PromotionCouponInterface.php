<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\Promotion\Model\PromotionCouponInterface as BasePromotionCouponInterface;

/**
 * @author Myke Hines <myke@webhines.com>
 */
interface PromotionCouponInterface extends BasePromotionCouponInterface
{
    /**
     * @return int|null
     */
    public function getPerCustomerUsageLimit();

    /**
     * @param int|null $perCustomerUsageLimit
     */
    public function setPerCustomerUsageLimit($perCustomerUsageLimit);
}
