<?php

namespace Sylius\Component\Inventory\Model;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Core\Model\InventoryUnitInterface as CoreInventoryUnitInterface;

class Prioritizer
{

    /**
     * @var CoreInventoryUnitInterface[]|Collection
     */
    protected $inventoryUnits;

    /**
     * @var Package[]|ArrayCollection
     */
    protected $packages;

    function __construct()
    {

    }

    public function prioritizePackages(Collection $inventoryUnits, ArrayCollection $packages) //Todo create interface combining Doctrine\Selectable and Doctrine\Collection
    {
        $this->inventoryUnits = $inventoryUnits;
        $this->packages = $packages;
        $this->sortPackages();
        $this->adjustPackages();
        $this->prunePackages();

        return $this->packages;
    }

    protected function adjustPackages()
    {
        foreach ($this->inventoryUnits as $unit) {
            $adjuster = new Adjuster($unit, ShipmentInterface::STATE_ONHOLD);
            $unit->setShippingState(ShipmentInterface::STATE_ONHOLD);

            $this->visitPackages($adjuster);

            $adjuster->setState(ShipmentInterface::STATE_BACKORDERED);
            $unit->setShippingState(ShipmentInterface::STATE_BACKORDERED);

            $this->visitPackages($adjuster);
        }
    }

    protected function sortPackages()
    {
        //Sort packages by amount of items they can deliver.
        //Items with most OnHold status get priority.
        $criteria = Criteria::create();
        $criteria->orderBy(array('onHoldQuantity' => 'DESC'));
        $this->packages = $this->packages->matching($criteria);
    }

    protected function prunePackages()
    {
        foreach($this->packages as $package) {
            if($package->isEmpty()) {
                $this->packages->removeElement($package);
            }
        }
    }

    private function visitPackages(Adjuster $adjuster) {
        foreach($this->packages as $package) {
            $hasItem = $package->hasItem($adjuster->getInventoryUnit(), $adjuster->getState());
            if($hasItem) {
                $adjuster->adjust($package);
            }
        }
    }
}