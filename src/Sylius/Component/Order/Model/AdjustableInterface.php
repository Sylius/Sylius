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

namespace Sylius\Component\Order\Model;

use Doctrine\Common\Collections\Collection;

interface AdjustableInterface
{
    /**
     * @return Collection<array-key, AdjustmentInterface>
     */
    public function getAdjustments(?string $type = null): Collection;

    public function addAdjustment(AdjustmentInterface $adjustment): void;

    public function removeAdjustment(AdjustmentInterface $adjustment): void;

    public function getAdjustmentsTotal(?string $type = null): int;

    public function removeAdjustments(?string $type = null): void;

    /**
     * Recalculates adjustments total. Should be used after adjustment change.
     */
    public function recalculateAdjustmentsTotal(): void;
}
