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

use Sylius\Component\Promotion\Model\PromotionCoupon as BasePromotionCoupon;

class PromotionCoupon extends BasePromotionCoupon implements PromotionCouponInterface
{
    /**
     * @var int|null
     */
    protected $perCustomerUsageLimit;

    /**
     * {@inheritdoc}
     */
    public function getPerCustomerUsageLimit(): ?int
    {
        return $this->perCustomerUsageLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function setPerCustomerUsageLimit(?int $perCustomerUsageLimit): void
    {
        $this->perCustomerUsageLimit = $perCustomerUsageLimit;
    }
}
