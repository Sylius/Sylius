<?php

namespace spec\Sylius\Bundle\InventoryBundle\Model;

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;

/**
 * Stockable item model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryUnit extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Model\InventoryUnit');
    }

    function it_should_be_a_Sylius_inventory_unit()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_defined_stockable_subject_by_default()
    {
        $this->getStockable()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_allow_defining_stockable_subject($stockable)
    {
        $this->setStockable($stockable);
        $this->getStockable()->shouldReturn($stockable);
    }

    function it_should_have_sold_state_by_default()
    {
        $this->getInventoryState()->shouldReturn(InventoryUnitInterface::STATE_SOLD);
    }

    function its_state_should_be_mutable()
    {
        $this->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED);
        $this->getInventoryState()->shouldReturn(InventoryUnitInterface::STATE_BACKORDERED);
    }

    function it_should_be_sold_if_its_state_says_so()
    {
        $this->shouldBeSold();
    }

    function it_should_be_backordered_if_its_state_says_so()
    {
        $this->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED);
        $this->shouldBeBackordered();
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_return_its_stockable_name($stockable)
    {
        $stockable->getInventoryName()->willReturn('[IPHONE5] iPhone 5');
        $this->setStockable($stockable);

        $this->getInventoryName()->shouldReturn('[IPHONE5] iPhone 5');
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_return_its_stockable_sku($stockable)
    {
        $stockable->getSku()->willReturn('IPHONE5');
        $this->setStockable($stockable);

        $this->getSku()->shouldReturn('IPHONE5');
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
