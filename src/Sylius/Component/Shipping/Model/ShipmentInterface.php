<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShipmentInterface extends ResourceInterface, ShippingSubjectInterface, TimestampableInterface
{
    const STATE_CART = 'cart';
    const STATE_READY = 'ready';
    const STATE_SHIPPED = 'shipped';
    const STATE_CANCELLED = 'cancelled';

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $state
     */
    public function setState($state);

    /**
     * @return ShippingMethodInterface
     */
    public function getMethod();

    /**
     * @param ShippingMethodInterface $method
     */
    public function setMethod(ShippingMethodInterface $method = null);

    /**
     * @return Collection|ShipmentUnitInterface[]
     */
    public function getUnits();

    /**
     * @param ShipmentUnitInterface $unit
     */
    public function addUnit(ShipmentUnitInterface $unit);

    /**
     * @param ShipmentUnitInterface $unit
     */
    public function removeUnit(ShipmentUnitInterface $unit);

    /**
     * @param ShipmentUnitInterface $unit
     *
     * @return bool
     */
    public function hasUnit(ShipmentUnitInterface $unit);

    /**
     * @return string
     */
    public function getTracking();

    /**
     * @param string $tracking
     */
    public function setTracking($tracking);

    /**
     * @return bool
     */
    public function isTracked();
}
