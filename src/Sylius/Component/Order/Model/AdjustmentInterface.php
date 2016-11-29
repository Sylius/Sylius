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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AdjustmentInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return AdjustableInterface
     */
    public function getAdjustable();

    /**
     * @param AdjustableInterface|null $adjustable
     */
    public function setAdjustable(AdjustableInterface $adjustable = null);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     */
    public function setLabel($label);

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @param int $amount
     */
    public function setAmount($amount);

    /**
     * @return bool
     */
    public function isNeutral();

    /**
     * @param bool $neutral
     */
    public function setNeutral($neutral);

    /**
     * @return bool
     */
    public function isLocked();

    public function lock();

    public function unlock();

    /**
     * Adjustments with amount < 0 are called "charges".
     *
     * @return bool
     */
    public function isCharge();

    /**
     * Adjustments with amount > 0 are called "credits".
     *
     * @return bool
     */
    public function isCredit();

    /**
     * @return string
     */
    public function getOriginCode();

    /**
     * @param string $originCode
     */
    public function setOriginCode($originCode);
}
