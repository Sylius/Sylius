<?php

namespace Sylius\Component\Inventory\Model;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\InventoryUnitInterface as CoreInventoryUnitInterface;
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

    public function hasItem(CoreInventoryUnitInterface $inventoryUnit, $state = null)
    {

        return $this->content->exists(function ($k, $item) use ($inventoryUnit, $state) {
                /* @var $item CoreInventoryUnitInterface */
                /* @var $inventoryUnit CoreInventoryUnitInterface */
                $sameObj = $item == $inventoryUnit;
                $sameState = !$state || $item->getShippingState() === $state;

                return $item == $inventoryUnit;
            });
    }

    public function isEmpty()
    {
        return $this->content->count() <= 0;
    }

    public function addInventoryUnit(CoreInventoryUnitInterface $unit)
    {
        $this->content->add($unit);
    }

    public function removeInventoryUnit(CoreInventoryUnitInterface $unit)
    {
        $key = array_search($unit, $this->content->toArray());
        $this->content->remove($key);
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getOnHoldQuantity() {
        return $this->getQuantity(InventoryUnitInterface::STATE_ONHOLD);
    }

    public function getQuantity($state = null)
    {
        $matched = ($state == null)
            ? $this->content
            : $this->content->filter(
                function ($e) use ($state) {
                    return $e->getShippingState() === $state;
                }
            );
        return $matched->count();
    }
}