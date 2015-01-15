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
 * Adjustable model interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AdjustableInterface
{
    /**
     * Return all adjustments attached to adjustable subject.
     *
     * @param null|string $type
     *
     * @return Collection|AdjustmentInterface[]
     */
    public function getAdjustments($type = null);

    /**
     * Add adjustment.
     *
     * @param AdjustmentInterface $adjustment
     */
    public function addAdjustment(AdjustmentInterface $adjustment);

    /**
     * Remove adjustment.
     *
     * @param AdjustmentInterface $adjustment
     */
    public function removeAdjustment(AdjustmentInterface $adjustment);

    /**
     * Get adjustments total.
     *
     * @param null|string $type
     *
     * @return integer
     */
    public function getAdjustmentsTotal($type = null);

    /**
     * Remove adjustment.
     *
     * @param string $type
     */
    public function removeAdjustments($type);

    /**
     * Clears all adjustments.
     */
    public function clearAdjustments();

    /**
     * Calculate adjustments total.
     */
    public function calculateAdjustmentsTotal();
}
