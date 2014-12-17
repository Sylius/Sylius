<?php

namespace Sylius\Component\Inventory\Model;


use Doctrine\Common\Collections\Collection;

class Prioritizer
{

    /**
     * @var InventoryUnitInterface[]|Collection
     */
    protected $inventoryUnits;

    /**
     * @var Package[]|Collection
     */
    protected $packages;

    function __construct()
    {

    }

    public function prioritizePackages(Collection $inventoryUnits, Collection $packages)
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
            $adjuster = new Adjuster($unit, InventoryUnitInterface::STATE_ONHOLD);

            $this->visitPackages($adjuster);

            $adjuster->setState(InventoryUnitInterface::STATE_BACKORDERED);

            $this->visitPackages($adjuster);
        }
    }

    protected function sortPackages()
    {

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
            $item = $package->findItem($adjuster->getInventoryUnit(), $adjuster->getState());
            if($item) {
                $adjuster->adjust($package);
            }
        }
    }
}