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

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Shipment item interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ShipmentItemInterface extends TimestampableInterface
{
    // Shipment item default states.
    const STATE_READY    = 'ready';
    const STATE_PENDING  = 'pending';
    const STATE_SHIPPED  = 'shipped';
    const STATE_RETURNED = 'returned';

    /**
     * Get shipment.
     *
     * @return ShipmentInterface
     */
    public function getShipment();

    /**
     * Define shipment.
     *
     * @param ShipmentInterface|null $shipment
     */
    public function setShipment(ShipmentInterface $shipment = null);

    /**
     * Get shippable object.
     *
     * @return ShippableInterface
     */
    public function getShippable();

    /**
     * Set shippable object.
     *
     * @param ShippableInterface $shippable
     */
    public function setShippable(ShippableInterface $shippable);

    /**
     * Get shipment item state.
     *
     * @return string
     */
    public function getShippingState();

    /**
     * Define shipment item state.
     *
     * @param string $state
     */
    public function setShippingState($state);
}
