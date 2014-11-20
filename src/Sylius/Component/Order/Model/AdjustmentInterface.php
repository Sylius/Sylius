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

use Sylius\Component\Originator\Model\OriginAwareInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Adjustment interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AdjustmentInterface extends TimestampableInterface, OriginAwareInterface
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
     * @return int
     */
    public function getAmount();

    /**
     * Set the amount.
     *
     * @param int $amount
     */
    public function setAmount($amount);

    /**
     * Is adjustment neutral?
     *
     * @return bool
     */
    public function isNeutral();

    /**
     * Modify the neutrality of the adjustment.
     *
     * @param bool $neutral
     */
    public function setNeutral($neutral);

    /**
     * Is charge?
     *
     * Adjustments with amount < 0 are called "charges".
     *
     * @return bool
     */
    public function isCharge();

    /**
     * Is credit?
     *
     * Adjustments with amount > 0 are called "credits".
     *
     * @return bool
     */
    public function isCredit();

    /**
     * Is adjustment locked?
     *
     * @return bool
     */
    public function isLocked();

    /**
     * Lock the adjustment.
     */
    public function lock();

    /**
     * Unlock the adjustment.
     */
    public function unlock();
}
