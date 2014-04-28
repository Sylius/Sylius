<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Finite\Factory\FactoryInterface;
use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Order\InventoryUnitTransitions;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\ShipmentItemTransitions;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Inventory unit listener.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InventoryUnitListener
{
    protected $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Update inventory unit state.
     *
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function updateState(GenericEvent $event)
    {
        $unit = $this->getInventoryUnit($event);

        $transitionName = $event->getArgument('transition');

        $this->factory->get($unit, InventoryUnitTransitions::GRAPH)->apply($transitionName);
        $this->factory->get($unit, ShipmentItemTransitions::GRAPH)->apply($transitionName);
    }

    private function getInventoryUnit(GenericEvent $event)
    {
        $unit = $event->getSubject();

        if (!$unit instanceof InventoryUnitInterface) {
            throw new UnexpectedTypeException($unit, 'Sylius\Component\Core\Model\InventoryUnitInterface');
        }

        return $unit;
    }
}
