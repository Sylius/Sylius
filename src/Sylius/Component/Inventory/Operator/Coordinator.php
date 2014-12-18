<?php
namespace Sylius\Component\Inventory\Operator;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ShipmentRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\ShipmentFactoryInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\Package;
use Sylius\Component\Inventory\Model\Packer;
use Sylius\Component\Inventory\Model\Prioritizer;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

class Coordinator
{

    /**
     * @var RepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * @var Packer
     */
    protected $packer;

    /**
     * @var ShipmentInterface[]
     */
    protected $packages;


    /**
     * @var OrderInterface
     */
    protected $order;


    /**
     * @var InventoryUnitInterface[]
     */
    protected $inventoryUnits;

    /**
     * @param array $stockLocations
     */
    public function __construct(
        RepositoryInterface $stockLocationRepo,
        Packer $packer,
        RepositoryInterface $shipmentRepository
    ) {
        $this->stockLocations = $stockLocationRepo->findAll();
        $this->packer = $packer;
        $this->shipmentRepository = $shipmentRepository;
        $this->packages = new ArrayCollection();
    }

    public function getShipments(OrderInterface $order)
    {
        $shipments = array();
        $this->order = $order;
        $this->inventoryUnits = $order->getInventoryUnits();
        foreach ($this->getPackages() as $package) {
            $shipments[] = $this->createShipment($package);
        }

        return $shipments;
    }


    private function getPackages()
    {
        $this->buildPackages();
        $this->prioritizePackages();
        $this->estimatePackages();

        return $this->packages;
    }

    private function buildPackages()
    {
        foreach ($this->stockLocations as $location) {

            //TODO check is stocklocation has at least a single item for an order.

            $locationPackages = $this->packer->getPackages($location, $this->order);

            foreach ($locationPackages as $locationPackage) {
                $this->packages->add($locationPackage);
            }
        }
    }

    private function prioritizePackages()
    {
         $prioritizer = new Prioritizer();
        $this->packages = $prioritizer->prioritizePackages($this->inventoryUnits, $this->packages);
    }

    private function estimatePackages()
    {
        //TODO estimate the price for each package
    }

    private function createShipment(Package $package)
    {
        /* @var $shipment ShipmentInterface */
        $shipment = $this->shipmentRepository->createNew();
        $shipment->setLocation($package->getLocation());

        foreach ($package->getContent() as $item) {
            $shipment->addItem($item);
        }

        return $shipment;
    }
}