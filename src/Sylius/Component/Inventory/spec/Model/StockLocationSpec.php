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
use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Inventory\Model\StockMovementInterface;

/**
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class StockLocationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Model\StockLocation');
    }

    function it_implements_Sylius_channel_interface()
    {
        $this->shouldImplement('Sylius\Component\Inventory\Model\StockLocationInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('SL 1');
        $this->getCode()->shouldReturn('SL 1');
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Warehouse A');
        $this->getName()->shouldReturn('Warehouse A');
    }

    function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    function it_can_be_disabled()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
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

    function its_stock_items_is_collection()
    {
        $this->getStockItems()->shouldHaveType('Doctrine\Common\Collections\ArrayCollection');
    }

    function it_has_no_stock_items_by_default()
    {
        $this->getStockItems()->count()->shouldReturn(0);
    }

    function its_stock_items_are_mutable(StockItemInterface $stockItem)
    {
        $this->hasStockItem($stockItem)->shouldReturn(false);
        $this->addStockItem($stockItem);
        $this->hasStockItem($stockItem)->shouldReturn(true);
    }

    function it_can_remove_stock_items(StockItemInterface $stockItem)
    {
        $this->addStockItem($stockItem);
        $this->hasStockItem($stockItem)->shouldReturn(true);

        $this->removeStockItem($stockItem);
        $this->hasStockItem($stockItem)->shouldReturn(false);
    }
}
