<?php

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

/*
 * @author Piotr Walków <walkow.piotr@gmail.com>
 */
class AdjustmentSubscriber implements EventSubscriberInterface
{
    const EVENT_ARGUMENT_DATA_KEY = 'adjustment-data';

    /** @var FactoryInterface */
    private $adjustmentFactory;

    public function __construct(
        FactoryInterface $adjustmentFactory
    ) {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    public static function getSubscribedEvents() {
        return [
            AdjustmentEvent::ADJUSTMENT_ADDING_ORDER => 'addAdjustmentOnOrder',
            AdjustmentEvent::ADJUSTMENT_ADDING_INVENTORY_UNIT => 'addAdjustmentOnInventoryUnit'
        ];
    }

    /**
     * @param GenericEvent $event
     */
    public function addAdjustmentOnOrder(GenericEvent $event)
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \UnexpectedValueException();
        }

        $this->setDataOnAdjustable(
            $event->getArgument(self::EVENT_ARGUMENT_DATA_KEY),
            $order)
        ;
    }

    /**
     * @param GenericEvent $event
     */
    public function addAdjustmentOnInventoryUnit(GenericEvent $event)
    {
        /** @var InventoryUnitInterface $inventoryUnit */
        $inventoryUnit = $event->getSubject();

        if (!$inventoryUnit instanceof InventoryUnitInterface) {
            throw new \UnexpectedValueException();
        }

        $this->setDataOnAdjustable(
            $event->getArgument('' . self::EVENT_ARGUMENT_DATA_KEY . ''),
            $inventoryUnit)
        ;
    }

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