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

use Sylius\Bundle\CoreBundle\Event\AdjustmentEvent;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\AdjustmentDTO;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author  Pete Ward <peter.ward@reiss.com>
 * @author  Piotr Walków <walkowpiotr@gmail.com>
 */
class AdjustmentSubscriber implements EventSubscriberInterface
{
    const EVENT_ARGUMENT_DATA_KEY = 'adjustment-data';

    /**
     * @var FactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @param FactoryInterface $adjustmentFactory
     */
    public function __construct(FactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * @return AdjustmentEvent[]
     */
    public static function getSubscribedEvents() {
        return [
            AdjustmentEvent::ADJUSTMENT_ADDING_ORDER => 'addAdjustmentToOrder',
            AdjustmentEvent::ADJUSTMENT_ADDING_INVENTORY_UNIT => 'addAdjustmentToInventoryUnit'
        ];
    }

    /**
     * @param GenericEvent $event
     */
    public function addAdjustmentToOrder(GenericEvent $event)
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \UnexpectedValueException();
        }

        $this->setDataOnAdjustable($event->getArgument(self::EVENT_ARGUMENT_DATA_KEY), $order);
    }

    /**
     * @param GenericEvent $event
     */
    public function addAdjustmentToInventoryUnit(GenericEvent $event)
    {
        /** @var InventoryUnitInterface $inventoryUnit */
        $inventoryUnit = $event->getSubject();

        if (!$inventoryUnit instanceof InventoryUnitInterface) {
            throw new \UnexpectedValueException();
        }

        $this->setDataOnAdjustable($event->getArgument(self::EVENT_ARGUMENT_DATA_KEY), $inventoryUnit);
    }

    /**
     * @param AdjustmentDTO       $adjustmentDTO
     * @param AdjustableInterface $adjustable
     */
    private function setDataOnAdjustable(AdjustmentDTO $adjustmentDTO, AdjustableInterface $adjustable)
    {
        /** @var AdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType($adjustmentDTO->type);
        $adjustment->setAmount($adjustmentDTO->amount);
        $adjustment->setDescription($adjustmentDTO->description);
        $adjustment->setNeutral($adjustmentDTO->neutrality);
        $adjustment->setOriginId($adjustmentDTO->originId);
        $adjustment->setOriginType($adjustmentDTO->originType);

        $adjustment->setAdjustable($adjustable);
        $adjustable->addAdjustment($adjustment);
    }
}