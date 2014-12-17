<?php

namespace Sylius\Component\Inventory\Model;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderInterface;

class Package
{

    /**
     * @var Collection
     */
    protected $content;

    /**
     * @var StockLocationInterface
     */
    protected $location;

    /**
     * @var OrderInterface
     */
    protected $order;

    public function __construct(StockLocationInterface $location, OrderInterface $order)
    {
        $this->location = $location;
        $this->order = $order;
        $this->content = new ArrayCollection();
    }

    public function quantity($state = null)
    {
        $matched = ($state == null)
            ? $this->content
            : $this->content->filter(
                function ($e) use ($state) {
                    return $e->getInventoryState();
                }
            );
        //TODO finish
    }

    public function findItem(InventoryUnitInterface $inventoryUnit, $state = null)
    {
        return $this->content->filter(
            function ($item) use ($inventoryUnit, $state) {
                return $item === $inventoryUnit && (!$state || $item->getInventoryState() === $state);
            }
        )->first();
    }

    public function isEmpty()
    {
        return $this->content <= 0;
    }

    public function addInventoryUnit(InventoryUnitInterface $unit)
    {
        $this->content->add($unit);
    }

    public function removeInventoryUnit(InventoryUnitInterface $unit)
    {
        $this->content->removeElement($unit);
    }

    public function getLocation() {
        return $this->location;
    }

    public function getContent() {
        retunr $this->content;
    }
}