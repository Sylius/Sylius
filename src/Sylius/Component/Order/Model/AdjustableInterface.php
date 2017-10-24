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

use Doctrine\Common\Collections\Collection;

interface AdjustableInterface
{
    /**
     * @param string|null $type
     *
     * @return Collection|AdjustmentInterface[]
     */
    public function getAdjustments(?string $type = null): Collection;

    /**
     * @param AdjustmentInterface $adjustment
     */
    public function addAdjustment(AdjustmentInterface $adjustment): void;

    /**
     * @param AdjustmentInterface $adjustment
     */
    public function removeAdjustment(AdjustmentInterface $adjustment): void;

    /**
     * @param string|null $type
     *
     * @return int
     */
    public function getAdjustmentsTotal(?string $type = null): int;

    /**
     * @param string|null $type
     */
    public function removeAdjustments(?string $type = null): void;

    /**
     * Recalculates adjustments total. Should be used after adjustment change.
     */
    public function recalculateAdjustmentsTotal(): void;
}
