<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;
use Sylius\Component\Inventory\Model\StockMovementInterface;

/**
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class StockItemSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Model\StockItem');
    }

    function it_implements_Sylius_channel_interface()
    {
        $this->shouldImplement('Sylius\Component\Inventory\Model\StockItemInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_stockable_by_default()
    {
        $this->getStockable()->shouldReturn(null);
    }

    function its_stockable_is_mutable(StockableInterface $stockable)
    {
        $this->setStockable($stockable);
        $this->getStockable()->shouldReturn($stockable);
    }

    function it_has_no_location_by_default()
    {
        $this->getLocation()->shouldReturn(null);
    }

    function its_locale_is_mutable(StockLocationInterface $stockLocation)
    {
        $this->setLocation($stockLocation);
        $this->getLocation()->shouldReturn($stockLocation);
    }

    function it_has_zero_on_hand_by_default()
    {
        $this->getOnHand()->shouldReturn(0);
    }

    function its_on_hand_is_mutable()
    {
        $this->setOnHand(4);
        $this->getOnHand()->shouldReturn(4);
    }

    function it_has_zero_on_hold_by_default()
    {
        $this->getOnHold()->shouldReturn(0);
    }

    function its_on_hold_is_mutable()
    {
        $this->setOnHold(2);
        $this->getOnHold()->shouldReturn(2);
    }

    function its_stock_movements_is_collection()
    {
        $this->getStockMovements()->shouldHaveType('Doctrine\Common\Collections\ArrayCollection');
    }

    function it_has_no_stock_movements_by_default()
    {
        $this->getStockMovements()->count()->shouldReturn(0);
    }

    function its_stock_movements_are_mutable(StockMovementInterface $movement)
    {
        $this->hasStockMovement($movement)->shouldReturn(false);
        $this->addStockMovement($movement);
        $this->hasStockMovement($movement)->shouldReturn(true);
    }

    function it_can_remove_stock_movements(StockMovementInterface $movement)
    {
        $this->addStockMovement($movement);
        $this->hasStockMovement($movement)->shouldReturn(true);

        $this->removeStockMovement($movement);
        $this->hasStockMovement($movement)->shouldReturn(false);
    }

    function it_has_no_available_on_demand_by_default()
    {
        $this->isAvailableOnDemand()->shouldReturn(false);
    }

    function its_available_on_demand_is_mutable()
    {
        $this->isAvailableOnDemand()->shouldReturn(false);
        $this->setAvailableOnDemand(true);
        $this->isAvailableOnDemand()->shouldReturn(true);
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable(\DateTime $date)
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(\DateTime $date)
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
