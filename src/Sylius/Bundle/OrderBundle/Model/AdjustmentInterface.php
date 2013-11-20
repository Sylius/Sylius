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

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Adjustment interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface AdjustmentInterface extends TimestampableInterface
{
    /**
     * Get adjustment subject.
     *
     * @return AdjustableInterface
     */
    public function getAdjustable();

    /**
     * Set adjustable.
     *
     * @param AdjustableInterface|null $adjustable
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
     * Get short description of adjustment.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set short description of adjustment.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get the adjustment amount.
     *
     * @return integer
     */
    public function getAmount();

    /**
     * Set the amount.
     *
     * @param integer $amount
     */
    public function setAmount($amount);

    /**
     * Is adjustment neutral?
     *
     * @return Boolean
     */
    public function isNeutral();

    /**
     * Modify the neutrality of the adjustment.
     *
     * @param Boolean $neutral
     */
    public function setNeutral($neutral);

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
}
