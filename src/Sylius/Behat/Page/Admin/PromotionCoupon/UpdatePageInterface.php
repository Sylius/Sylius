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

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @param int $limit
     */
    public function setCustomerUsageLimit(int $limit): void;

    /**
     * @param \DateTimeInterface $date
     */
    public function setExpiresAt(\DateTimeInterface $date): void;

    /**
     * @param int $limit
     */
    public function setUsageLimit(int $limit): void;
}
