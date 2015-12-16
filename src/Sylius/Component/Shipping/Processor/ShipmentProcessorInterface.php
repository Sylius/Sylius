<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Processor;

use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentItemInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ShipmentProcessorInterface
{
    /**
     * @param ShipmentInterface[] $shipments
     * @param string              $transition ShipmentTransitions::*
     */
    public function updateShipmentStates($shipments, $transition);

    /**
     * @param ShipmentItemInterface[] $items
     * @param string                  $transition ShipmentItemTransitions::*
     */
    public function updateItemStates($items, $transition);
}
