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

use Sylius\Component\Promotion\Model\PromotionCouponInterface as BasePromotionCouponInterface;

interface PromotionCouponInterface extends BasePromotionCouponInterface
{
    public function getPerCustomerUsageLimit(): ?int;

    public function setPerCustomerUsageLimit(?int $perCustomerUsageLimit): void;

    public function isReusableFromCancelledOrders(): bool;

    public function setReusableFromCancelledOrders(bool $reusableFromCancelledOrders): void;
}
