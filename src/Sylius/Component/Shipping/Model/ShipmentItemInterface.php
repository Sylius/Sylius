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

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Shipment item interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShipmentItemInterface extends TimestampableInterface
{
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
