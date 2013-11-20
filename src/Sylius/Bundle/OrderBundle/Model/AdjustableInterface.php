<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Adjustable model interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface AdjustableInterface
{
    /**
     * Return all adjustments attached to adjustable subject.
     *
     * @return Collection
     */
    public function getAdjustments();

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
     * @return integer
     */
    public function getAdjustmentsTotal();

    /**
     * Calculate adjustments total.
     */
    public function calculateAdjustmentsTotal();
}
