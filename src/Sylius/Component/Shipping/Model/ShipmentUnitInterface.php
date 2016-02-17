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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShipmentUnitInterface extends TimestampableInterface, ResourceInterface
{
    /**
     * @return ShipmentInterface
     */
    public function getShipment();

    /**
     * @param ShipmentInterface|null $shipment
     */
    public function setShipment(ShipmentInterface $shipment = null);

    /**
     * @return ShippableInterface
     */
    public function getShippable();

    /**
     * @return string
     */
    public function getShippingState();

    /**
     * @param string $state
     */
    public function setShippingState($state);
}
