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

namespace Sylius\Component\Order\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface AdjustmentInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return AdjustableInterface|null
     */
    public function getAdjustable(): ?AdjustableInterface;

    /**
     * @param AdjustableInterface|null $adjustable
     */
    public function setAdjustable(?AdjustableInterface $adjustable): void;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void;

    /**
     * @return string|null
     */
    public function getLabel(): ?string;

    /**
     * @param string|null $label
     */
    public function setLabel(?string $label): void;

    /**
     * @return int
     */
    public function getAmount(): int;

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void;

    /**
     * @return bool
     */
    public function isNeutral(): bool;

    /**
     * @param bool $neutral
     */
    public function setNeutral(bool $neutral): void;

    /**
     * @return bool
     */
    public function isLocked(): bool;

    public function lock(): void;

    public function unlock(): void;

    /**
     * Adjustments with amount < 0 are called "charges".
     *
     * @return bool
     */
    public function isCharge(): bool;

    /**
     * Adjustments with amount > 0 are called "credits".
     *
     * @return bool
     */
    public function isCredit(): bool;

    /**
     * @return string|null
     */
    public function getOriginCode(): ?string;

    /**
     * @param string|null $originCode
     */
    public function setOriginCode(?string $originCode): void;
}
