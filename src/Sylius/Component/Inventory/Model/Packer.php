<?php

namespace Sylius\Component\Inventory\Model;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ShipmentRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

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
        /* @var $variantUnits InventoryUnitInterface[]|Collection */
        foreach ($grouped as $stockable => $variantUnits) {
            $units = clone $grouped->getInfo();

            $stockable = $grouped->current();
            $quantity = $units->count();
            if (!$stockable->isAvailableOnDemand()) {
                if (!$stockItem = $location->getStockItem($stockable)) {
                    continue;
                }

                $backordered = 0;
                if ($stockItem->getOnHand() >= $quantity) {
                    $onHand = $units->count();
                } else {
                    $onHand = $stockItem->getOnHand();
                    $backordered = $quantity - $onHand;
                }

                /* @var $shippingUnit InventoryUnitInterface */
                foreach ($units->slice(0, $onHand) as $shippingUnit) {
                    $package->addInventoryUnit($shippingUnit);
                }

                foreach ($units->slice(0, $backordered) as $shippingUnit) {
                    $shippingUnit->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED);
                    $package->addInventoryUnit($shippingUnit);
                }
            } else {
                /* @var $shippingUnit InventoryUnitInterface */
                foreach ($units->slice(0, $quantity) as $shippingUnit) {
                    $package->addInventoryUnit($shippingUnit);
                }
            }
        }

        //TODO insert a splitter here which splits the shipment in multiple smaller shipments based on a set of rules (size, weight)
        return array($package);
    }
}