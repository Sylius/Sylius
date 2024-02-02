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

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function isCodeDisabled(): bool;

    public function setCustomerUsageLimit(int $limit): void;

    public function setExpiresAt(\DateTimeInterface $date): void;

    public function setUsageLimit(string $limit): void;

    public function isReusableFromCancelledOrders(): bool;

    public function toggleReusableFromCancelledOrders(bool $reusable): void;
}
