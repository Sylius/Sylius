<?php

namespace Sylius\Component\Inventory\Model;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection; //TODO Add doctrine in composer.json
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ShipmentRepository; //TODO move repo to component
use Sylius\Component\Core\Model\InventoryUnitInterface as CoreInventoryUnitInterface; //TODO don't depend on core, move shipmentSate from Core/InventoryUnitIterface to Inventory/InventoryUnitInterface
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface; //TODO Add shipment in composer.json or move this to shipment comp.

class Packer
{
    /**
     * @var ShipmentRepository
     */
    protected $shipmentRepository;
    protected $packages;

    public function __construct(RepositoryInterface $repo)
    {
        $this->shipmentRepository = $repo;
    }

    public function getPackages(StockLocationInterface $location, OrderInterface $order)
    {

        $package = new Package($location, $order);

        //Group the inventory by their stockable
        $grouped = new \SplObjectStorage();
        $units = $order->getInventoryUnits();

        //Group by stockable
        foreach ($units as $unit) {
            $collection = new ArrayCollection();
            $stockable = $unit->getStockable();
            if ($grouped->contains($stockable)) {
               $collection = $grouped->offsetGet($stockable);
            }
            $collection->add($unit);
            $grouped->attach($stockable, $collection);
        }

        /* @var $stockable    StockableInterface */
        /* @var $variantUnits CoreInventoryUnitInterface[]|Collection */
        foreach ($grouped as $stockable => $variantUnits) {
            //$units = clone $grouped->getInfo(); //TODO clone content in collection as well!!!!!!
            $units = new ArrayCollection();
            foreach($grouped->getInfo() as $unit)
            {
                $units->add(clone $unit);
            }

            $stockable = $grouped->current();
            $quantity = $units->count();
            if (!$stockable->isAvailableOnDemand()) {
                if (!$stockItem = $location->getStockItem($stockable)) {
                    continue;
                }

                $backordered = 0;
                if ($stockItem->getOnHand() >= $quantity) {
                    $onHand = $quantity;
                } else {
                    $onHand = $stockItem->getOnHand();
                    $backordered = $quantity - $onHand;
                }


                //Set $onHand amount of InventoryUnits to ShippingState OnHold
                /* @var $shippingUnit CoreInventoryUnitInterface */
                foreach ($units->slice(0, $onHand) as $shippingUnit) {
                    $shippingUnit->setShippingState(ShipmentInterface::STATE_ONHOLD);
                    $package->addInventoryUnit($shippingUnit);
                }

                //Set $backordered amuont of InventoryUnits to ShippingState Backordered
                foreach ($units->slice($onHand, $backordered) as $shippingUnit) {
                    $shippingUnit->setShippingState(ShipmentInterface::STATE_BACKORDERED);
                    $package->addInventoryUnit($shippingUnit);
                }
            } else {
                /* @var $shippingUnit CoreInventoryUnitInterface */
                foreach ($units->slice(0, $quantity) as $shippingUnit) {
                    $shippingUnit->setShippingState(ShipmentInterface::STATE_ONHOLD);
                    $package->addInventoryUnit($shippingUnit);
                }
            }
        }

        //TODO insert a splitter here which splits the shipment in multiple smaller shipments based on a set of rules (size, weight)
        return array($package);
    }
}