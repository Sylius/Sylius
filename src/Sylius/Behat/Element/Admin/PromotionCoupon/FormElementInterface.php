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

namespace Sylius\Behat\Element\Admin\PromotionCoupon;

use Sylius\Behat\Element\Admin\Crud\FormElementInterface as BaseFormElementInterface;

interface FormElementInterface extends BaseFormElementInterface
{
    public function setUsageLimit(int $limit): void;

    public function setCustomerUsageLimit(int $limit): void;

    public function setExpiresAt(\DateTimeInterface $date): void;

    public function toggleReusableFromCancelledOrders(bool $reusable): void;

    public function isReusableFromCancelledOrders(): bool;
}
