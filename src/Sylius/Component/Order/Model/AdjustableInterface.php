<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AdjustableInterface
{
    /**
     * @param null|string $type
     *
     * @return Collection|AdjustmentInterface[]
     */
    public function getAdjustments($type = null);

    /**
     * @param AdjustmentInterface $adjustment
     */
    public function addAdjustment(AdjustmentInterface $adjustment);

    /**
     * @param AdjustmentInterface $adjustment
     */
    public function removeAdjustment(AdjustmentInterface $adjustment);

    /**
     * @param null|string $type
     *
     * @return int
     */
    public function getAdjustmentsTotal($type = null);

    /**
     * @param string $type
     */
    public function removeAdjustments($type);

    /**
     * Recalculates adjustments total. Should be used after adjustment change.
     */
    public function recalculateAdjustmentsTotal();
}
