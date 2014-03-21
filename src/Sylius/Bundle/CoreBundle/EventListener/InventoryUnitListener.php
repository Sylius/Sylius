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

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

/**
 * Inventory unit listener.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InventoryUnitListener
{
    /**
     * Update inventory unit state.
     *
     * @param GenericEvent $event
     */
    public function updateState(GenericEvent $event)
    {
        $unit = $this->getInventoryUnit($event);

        $state = $event->getArgument('state');

        $unit->setInventoryState($state);

        switch ($state) {
            case $unit::STATE_BACKORDERED:
                $unit->setShippingState(ShipmentInterface::STATE_ONHOLD);
                break;

            case $unit::STATE_SOLD:
                $unit->setShippingState(ShipmentInterface::STATE_READY);
                break;

            case $unit::STATE_RETURNED:
                $unit->setShippingState(ShipmentInterface::STATE_RETURNED);
                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unexpected inventory state "%s".', $state));
                break;
        }
    }

    private function getInventoryUnit(GenericEvent $event)
    {
        $unit = $event->getSubject();

        if (!$unit instanceof InventoryUnitInterface) {
            throw new \InvalidArgumentException(
                'Inventory unit listener requires event subject to be instance of "Sylius\Component\Core\Model\InventoryUnitInterface"'
            );
        }

        return $unit;
    }
}
