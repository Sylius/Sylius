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

namespace Sylius\Component\Inventory\Model;

interface StockableInterface
{
    /**
     * @return string|null
     */
    public function getInventoryName(): ?string;

    /**
     * @return bool
     */
    public function isInStock(): bool;

    /**
     * @return int|null
     */
    public function getOnHold(): ?int;

    /**
     * @param int|null $onHold
     */
    public function setOnHold(?int $onHold): void;

    /**
     * @return int|null
     */
    public function getOnHand(): ?int;

    /**
     * @param int|null $onHand
     */
    public function setOnHand(?int $onHand): void;

    /**
     * @return bool
     */
    public function isTracked(): bool;

    /**
     * @param bool $tracked
     */
    public function setTracked(bool $tracked): void;
}
