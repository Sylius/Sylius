<?php

namespace spec\Sylius\Bundle\InventoryBundle\Checker;

use PHPSpec2\ObjectBehavior;

/**
 * Stockable item model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class AvailabilityChecker extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(true);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Checker\AvailabilityChecker');
    }

    function it_should_be_Sylius_inventory_availability_checker()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface');
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_recognize_any_stockable_as_available_if_backorders_are_enabled($stockable)
    {
        $this->beConstructedWith(true);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_recognize_any_stockable_as_available_if_its_on_demand_and_backorders_are_disabled($stockable)
    {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(true);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_recognize_any_stockable_as_available_if_its_on_demand_and_backorders_are_disabled_and_on_hand_quantity_insufficient($stockable)
    {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(true);
        $stockable->getOnHand()->willReturn(0);

        $this->isStockAvailable($stockable)->shouldReturn(true);

        $stockable->isAvailableOnDemand()->willReturn(true);
        $stockable->getOnHand()->willReturn(-5);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_recognize_stockable_as_available_if_on_hand_quantity_is_greater_than_0($stockable)
    {
        $this->beConstructedWith(false);

        $stockable->getOnHand()->willReturn(5);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_recognize_stockable_as_available_even_if_hand_quantity_is_lesser_than_or_equal_to_0_when_backorders_are_enabled($stockable)
    {
        $this->beConstructedWith(true);

        $stockable->getOnHand()->willReturn(0);
        $this->isStockAvailable($stockable)->shouldReturn(true);

        $stockable->getOnHand()->willReturn(-5);
        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_recognize_stockable_as_not_available_if_on_hand_quantity_is_lesser_than_or_equal_to_0($stockable)
    {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(false);

        $stockable->getOnHand()->willReturn(0);
        $this->isStockAvailable($stockable)->shouldReturn(false);

        $stockable->getOnHand()->willReturn(-5);
        $this->isStockAvailable($stockable)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_recognize_any_stockable_and_quantity_as_sufficient_if_backorders_are_enabled($stockable)
    {
        $this->beConstructedWith(true);

        $this->isStockSufficient($stockable, 999)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_recognize_stockable_stock_sufficient_if_on_hand_quantity_is_greater_than_required_quantity($stockable)
    {
        $this->beConstructedWith(false);

        $stockable->getOnHand()->willReturn(10);
        $this->isStockSufficient($stockable, 5)->shouldReturn(true);

        $stockable->getOnHand()->willReturn(15);
        $this->isStockSufficient($stockable, 15)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_recognize_stock_sufficient_if_its_available_on_demand_and_backorders_are_disabled($stockable)
    {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(true);

        $stockable->getOnHand()->willReturn(0);
        $this->isStockSufficient($stockable, 999)->shouldReturn(true);

        $stockable->getOnHand()->willReturn(-5);
        $this->isStockSufficient($stockable, 3)->shouldReturn(true);
    }


}
