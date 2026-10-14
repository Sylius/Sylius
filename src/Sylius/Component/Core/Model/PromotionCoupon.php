<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\Promotion\Model\PromotionCoupon as BasePromotionCoupon;

class PromotionCoupon extends BasePromotionCoupon implements PromotionCouponInterface
{
    /** @var int */
    protected $version = 1;

    /** @var int|null */
    protected $perCustomerUsageLimit;

    /** @var bool */
    protected $reusableFromCancelledOrders = true;

    public function getPerCustomerUsageLimit(): ?int
    {
        return $this->perCustomerUsageLimit;
    }

    public function setPerCustomerUsageLimit(?int $perCustomerUsageLimit): void
    {
        $this->perCustomerUsageLimit = $perCustomerUsageLimit;
    }

    public function isReusableFromCancelledOrders(): bool
    {
        return $this->reusableFromCancelledOrders;
    }

    public function setReusableFromCancelledOrders(bool $reusableFromCancelledOrders): void
    {
        $this->reusableFromCancelledOrders = $reusableFromCancelledOrders;
    }

    public function getVersion(): ?int
    {
        trigger_deprecation(
            'sylius/core',
            '2.3',
            'The "%s()" method is deprecated since Sylius 2.3 and will be removed in Sylius 3.0.',
            __METHOD__,
        );

        return $this->version;
    }

    public function setVersion(?int $version): void
    {
        trigger_deprecation(
            'sylius/core',
            '2.3',
            'The "%s()" method is deprecated since Sylius 2.3 and will be removed in Sylius 3.0.',
            __METHOD__,
        );

        $this->version = $version;
    }
}
