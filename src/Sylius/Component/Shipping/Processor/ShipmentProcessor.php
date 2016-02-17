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

use Doctrine\Common\Collections\Collection;
use SM\Factory\FactoryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Sylius\Component\Shipping\ShipmentUnitTransitions;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShipmentProcessor implements ShipmentProcessorInterface
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function updateShipmentStates($shipments, $transition)
    {
        if (!is_array($shipments) && !$shipments instanceof Collection) {
            throw new \InvalidArgumentException('Shipments value must be array or instance of "Doctrine\Common\Collections\Collection".');
        }

        foreach ($shipments as $shipment) {
            if (!$shipment instanceof ShipmentInterface) {
                throw new UnexpectedTypeException($shipment, ShipmentInterface::class);
            }

            $this->factory->get($shipment, ShipmentTransitions::GRAPH)->apply($transition, true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateUnitStates($units, $transition)
    {
        if (!is_array($units) && !$units instanceof Collection) {
            throw new \InvalidArgumentException('Shipping units value must be array or instance of "Doctrine\Common\Collections\Collection".');
        }

        foreach ($units as $unit) {
            if (!$unit instanceof ShipmentUnitInterface) {
                throw new UnexpectedTypeException($unit, 'Sylius\Component\Shipping\Model\ShipmentUnitInterface');
            }

            $this->factory->get($unit, ShipmentUnitTransitions::GRAPH)->apply($transition, true);
        }
    }
}
