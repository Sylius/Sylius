<?php
namespace Sylius\Component\Inventory\Operator;


use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ShipmentRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\ShipmentFactoryInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

class Coordinator
{

    /**
     * @var StockLocationInterface[]
     */
    protected $stockLocations;

    /**
     * @var InventoryUnitInterface[]
     */
    protected $inventoryUnits;

    /**
     * @var PackerInterface
     */
    protected $packer;

    /**
     * @var ShipmentInterface[]
     */
    protected $packages;

    /**
     * @var RepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * @var OrderInterface
     */
    protected $order;


    /**
     * @param array $stockLocations
     */
    public function __construct(array $stockLocations, PackerInterface $packer, RepositoryInterface $shipmentRepository)
    {
        $this->stockLocations = $stockLocations;
        $this->packer = $packer;
        $this->shipmentRepository = $shipmentRepository;
    }

    public function getShipments(OrderInterface $order, array $inventoryUnits)
    {

        $this->order = $order;
        $this->inventoryUnits = $inventoryUnits;
    }

    private function getPackages()
    {
        $this->buildPackages();
        $this->prioritizePackages();
        $this->estimatePackages();
    }

    private function buildPackages()
    {
        foreach ($this->stockLocations as $location) {

            //TODO check is stocklocation has at least a single item for an order.

            $locationPackages = $this->getShipmentFromLocation($location, $this->inventoryUnits);
            $this->packages = array_merge($this->packages, $locationPackages);
        }
    }

    private function prioritizePackages()
    {
    }

    private function estimatePackages()
    {
    }

    /**
     * @param StockLocationInterface   $location
     * @param InventoryUnitInterface[] $inventoryUnits
     */
    private function getShipmentFromLocation(StockLocationInterface $location, OrderInterface $order)
    {
        /* @var $shipment ShipmentInterface */
        $shipment = $this->shipmentRepository->createNew();

        $checkedStockables = array();

        foreach ($order->getInventoryUnits() as $unit) {
            $stockable = $unit->getStockable();

            if (in_array($stockable, $checkedStockables)) {
                continue;
            }

            /* @var $items StockItemInterface[]|Collection */
            $items = $location->getItems()->filter(
                function ($entry) use ($stockable) {
                    return ($entry->getStockable() === $stockable);
                }
            );


            foreach($items as $item) {
                $locationStock = $item->getOnHand();
            }



            array_push($checkedStockables, $stockable);
            $shipment->addItem();
        }
    }
}