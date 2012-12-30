<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

/**
 * Adjustment interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface AdjustmentInterface
{
    /**
     * Get adjustment id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get adjustment subject.
     *
     * @return AdjustableInterface
     */
    public function getAdjustable();

    /**
     * Set adjustable.
     *
     * @param AdjustableInterface $adjustable
     */
    public function setAdjustable(AdjustableInterface $adjustable = null);

    /**
     * Get the label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set label.
     *
     * @param string $label
     */
    public function setLabel($label);

    /**
     * Get the adjustment amount.
     *
     * @return float
     */
    public function getAmount();

    /**
     * Set the amount.
     *
     * @param float $amount
     */
    public function setAmount($amount);

    /**
     * Is charge?
     *
     * Adjustments with amount < 0 are called "charges".
     *
     * @return Boolean
     */
    public function isCharge();

    /**
     * Is credit?
     *
     * Adjustments with amount > 0 are called "credits".
     *
     * @return Boolean
     */
    public function isCredit();

    /**
     * Get creation date.
     *
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * Get last update time.
     *
     * @return DateTime
     */
    public function getUpdatedAt();
}
