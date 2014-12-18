<?php

namespace Sylius\Component\Inventory\Model;


class Adjuster
{
    protected $state;
    protected $inventoryUnit;
    protected $fulfilled;

    public function __construct(InventoryUnitInterface $inventoryUnit, $state)
    {
        $this->inventoryUnit = $inventoryUnit;
        $this->state = $state;
        $this->fulfilled = false;
    }

    public function adjust(Package $package) {
        if($this->fulfilled) {
            $package->removeInventoryUnit($this->inventoryUnit);
        } else {
            $this->fulfilled = true;
        }
    }

    public function getInventoryUnit()
    {
        return $this->inventoryUnit;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }
}