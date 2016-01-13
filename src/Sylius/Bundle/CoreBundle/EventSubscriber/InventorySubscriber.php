<?php
namespace Sylius\Bundle\CoreBundle\EventSubscriber;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Inventory\SyliusStockItemEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class InventorySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            SyliusStockItemEvents::POST_INCREASE => array(
                array('syncVariantStockInfo', 0),
            ),
            SyliusStockItemEvents::POST_DECREASE => array(
                array('syncVariantStockInfo', 0),
            )
        );
    }

    public function syncVariantStockInfo(GenericEvent $event)
    {
        /** @var StockItemInterface $stockItem */
        $stockItem = $event->getSubject();

        /** @var ProductVariantInterface $variant */
        $variant = $stockItem->getStockable();

        $onHand = 0;
        $onHold = 0;
        $availableOnDemand = false;

        /** @var StockItemInterface $sItem */
        foreach($variant->getStockItems() as $sItem){
            $onHand += $sItem->getOnHand();
            $onHold += $sItem->getOnHold();
            $availableOnDemand = $availableOnDemand || $sItem->isAvailableOnDemand();
        }

        $variant->setOnHand($onHand);
        $variant->setOnHold($onHold);
        $variant->setAvailableOnDemand($availableOnDemand);
    }
}