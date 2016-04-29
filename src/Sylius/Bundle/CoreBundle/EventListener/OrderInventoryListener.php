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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderProcessing\InventoryHandlerInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderInventoryListener
{
    /**
     * @var InventoryHandlerInterface
     */
    protected $inventoryHandler;

    /**
     * @param InventoryHandlerInterface $inventoryHandler
     */
    public function __construct(InventoryHandlerInterface $inventoryHandler)
    {
        $this->inventoryHandler = $inventoryHandler;
    }

    /**
     * @param GenericEvent $event
     */
    public function holdInventoryUnits(GenericEvent $event)
    {
        $this->inventoryHandler->holdInventory(
            $this->getOrder($event)
        );
    }

    /**
     * @param GenericEvent $event
     */
    public function resolveInventoryState(GenericEvent $event)
    {
        $orderItem = $this->getItem($event);

        $state = PropertyAccess::createPropertyAccessor()->getValue(
            [
                OrderInterface::STATE_PENDING => InventoryUnitInterface::STATE_ONHOLD,
                OrderInterface::STATE_SHIPPED => InventoryUnitInterface::STATE_SOLD,
            ],
            sprintf('[%s]', $orderItem->getOrder()->getState())
        );

        if (null !== $state) {
            foreach ($orderItem->getUnits() as $itemUnit) {
                $itemUnit->setInventoryState($state);
            }
        }
    }

    /**
     * @param GenericEvent $event
     *
     * @return OrderInterface
     *
     * @throws UnexpectedTypeException
     */
    protected function getOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                OrderInterface::class
            );
        }

        return $order;
    }

    /**
     * @param GenericEvent $event
     *
     * @return OrderItemInterface
     *
     * @throws UnexpectedTypeException
     */
    protected function getItem(GenericEvent $event)
    {
        $item = $event->getSubject();

        if (!$item instanceof OrderItemInterface) {
            throw new UnexpectedTypeException(
                $item,
                'Sylius\Component\Core\Model\OrderItemInterface'
            );
        }

        return $item;
    }
}
