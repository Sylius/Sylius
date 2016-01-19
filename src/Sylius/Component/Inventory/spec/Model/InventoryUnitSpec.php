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
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryUnitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Model\InventoryUnit');
    }

    function it_implements_Sylius_inventory_unit_interface()
    {
        $this->shouldImplement(InventoryUnitInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_defined_stockable_subject_by_default()
    {
        $this->getStockable()->shouldReturn(null);
    }

    function it_allows_defining_stockable_subject(StockableInterface $stockable)
    {
        $this->setStockable($stockable);
        $this->getStockable()->shouldReturn($stockable);
    }

    function it_has_checkout_state_by_default()
    {
        $this->getInventoryState()->shouldReturn(InventoryUnitInterface::STATE_CHECKOUT);
    }

    function its_state_is_mutable()
    {
        $this->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED);
        $this->getInventoryState()->shouldReturn(InventoryUnitInterface::STATE_BACKORDERED);
    }

    function it_is_sold_if_its_state_says_so()
    {
        $this->setInventoryState(InventoryUnitInterface::STATE_SOLD);

        $this->shouldBeSold();
    }

    function it_is_backordered_if_its_state_says_so()
    {
        $this->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED);
        $this->shouldBeBackordered();
    }

    function it_returns_its_stockable_name(StockableInterface $stockable)
    {
        $stockable->getInventoryName()->willReturn('[IPHONE5] iPhone 5');
        $this->setStockable($stockable);

        $this->getInventoryName()->shouldReturn('[IPHONE5] iPhone 5');
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
