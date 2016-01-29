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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShipmentInterface extends ShippingSubjectInterface, ResourceInterface
{
    // Shipment default states.
    const STATE_CHECKOUT    = 'checkout';
    const STATE_ONHOLD      = 'onhold';
    const STATE_PENDING     = 'pending';
    const STATE_READY       = 'ready';
    const STATE_BACKORDERED = 'backordered';
    const STATE_SHIPPED     = 'shipped';
    const STATE_RETURNED    = 'returned';
    const STATE_CANCELLED   = 'cancelled';

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
    public function setMethod(ShippingMethodInterface $method);

    /**
     * @return Collection|ShipmentItemInterface[]
     */
    public function getItems();

    /**
     * @param ShipmentItemInterface $item
     */
    public function addItem(ShipmentItemInterface $item);

    /**
     * @param ShipmentItemInterface $item
     */
    public function removeItem(ShipmentItemInterface $item);

    /**
     * @param ShipmentItemInterface $item
     *
     * @return bool
     */
    public function hasItem(ShipmentItemInterface $item);

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
