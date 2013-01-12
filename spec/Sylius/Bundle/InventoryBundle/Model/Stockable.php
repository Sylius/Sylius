<?php

namespace spec\Sylius\Bundle\InventoryBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Stockable model spec.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class Stockable extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Model\Stockable');
    }

    function it_should_be_a_Sylius_stockable()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Model\StockableInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_defined_sku_by_default()
    {
        $this->getSku()->shouldReturn(null);
    }

    function its_sku_should_be_mutable()
    {
        $this->setSku('1234R');
        $this->getSku()->shouldReturn('1234R');
    }

    function it_should_not_have_defined_inventory_name_by_default()
    {
        $this->getInventoryName()->shouldReturn(null);
    }

    function its_inventory_name_should_be_mutable()
    {
        $this->setInventoryName('Lorem Ipsum');
        $this->getInventoryName()->shouldReturn('Lorem Ipsum');
    }

    function it_should_be_in_stock_by_default()
    {
        $this->isInStock()->shouldReturn(true);
    }

    function it_should_be_available_on_demand_by_default()
    {
        $this->isAvailableOnDemand()->shouldReturn(true);
    }

    function its_available_on_demand_should_be_mutable()
    {
        $this->setAvailableOnDemand(false);
        $this->isAvailableOnDemand()->shouldReturn(false);
    }

    function it_should_have_1_on_hand_by_default()
    {
        $this->getOnHand()->shouldReturn(1);
    }


    function its_on_hand_should_be_mutable()
    {
        $this->setOnHand(5);
        $this->getOnHand()->shouldReturn(5);
    }
}
