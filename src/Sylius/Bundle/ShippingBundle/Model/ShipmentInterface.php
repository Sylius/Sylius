<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Model;

/**
 * Shipment interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ShipmentInterface extends ShippingSubjectInterface
{
    // Shipment default states.
    const STATE_CHECKOUT    = 'checkout';
    const STATE_READY       = 'ready';
    const STATE_PENDING     = 'pending';
    const STATE_DISPATCHED  = 'dispatched';
    const STATE_SHIPPED     = 'shipped';
    const STATE_RETURNED    = 'returned';

    /**
     * Get shipment state.
     *
     * @return string
     */
    public function getState();

    /**
     * Define shipment state.
     *
     * @param string $state
     */
    public function setState($state);

    /**
     * Get shipping method.
     *
     * @return ShippingMethodInterface
     */
    public function getMethod();

    /**
     * Define shipping method.
     *
     * @param ShippingMethodInterface $method
     */
    public function setMethod(ShippingMethodInterface $method);

    /**
     * Get shipment items.
     *
     * @return ShipmentItemInterface[]
     */
    public function getItems();

    /**
     * Add shipment item.
     *
     * @param ShipmentItemInterface $item
     */
    public function addItem(ShipmentItemInterface $item);

    /**
     * Remove shipment item.
     *
     * @param ShipmentItemInterface $item
     */
    public function removeItem(ShipmentItemInterface $item);

    /**
     * Has shipment item?
     *
     * @param ShipmentItemInterface $item
     *
     * @return Boolean
     */
    public function hasItem(ShipmentItemInterface $item);

    /**
     * Get tracking code.
     *
     * @return string
     */
    public function getTracking();

    /**
     * Define tracking code.
     *
     * @param string $tracking
     */
    public function setTracking($tracking);

    /**
     * Check if this shipment has any tracking.
     *
     * @return Boolean
     */
    public function isTracked();
}
