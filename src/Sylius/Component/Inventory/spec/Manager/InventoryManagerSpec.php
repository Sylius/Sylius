<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Manager;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Model\StockInterface;
use Sylius\Component\Resource\Model\SoftDeletableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryManagerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(true);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Manager\InventoryManager');
    }

    function it_implements_Sylius_inventory_availability_checker_interface()
    {
        $this->shouldImplement('Sylius\Component\Inventory\Manager\InventoryManagerInterface');
    }

    function it_recognizes_any_stockable_as_available_if_backorders_are_enabled(StockableInterface $stockable, StockInterface $stock)
    {
        $this->beConstructedWith(true);

        $stock->isManageStock()->willReturn(false);
        $stock->hasAllowBackorders()->willReturn(false);
        $stockable->getStock()->willReturn($stock);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_any_stockable_as_available_if_its_manage_stock_and_backorders_are_disabled(
        StockableInterface $stockable,
        StockInterface $stock
    ) {
        $this->beConstructedWith(false);

        $stock->isManageStock()->willReturn(false);
        $stock->hasAllowBackorders()->willReturn(false);

        $stockable->getStock()->willReturn($stock);
        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_any_stockable_as_available_if_its_manage_stock_and_backorders_are_disabled_and_on_hand_quantity_insufficient(
        StockableInterface $stockable,
        StockInterface $stock        
    ) {
        $this->beConstructedWith(false);

        $stock->isManageStock()->willReturn(false);
        $stock->getOnHand()->willReturn(0);
        $stock->hasAllowBackorders()->willReturn(false);
        $stockable->getStock()->willReturn($stock);

        $this->isStockAvailable($stockable)->shouldReturn(true);

        $stock->getOnHand()->willReturn(-5);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_available_if_on_hand_quantity_is_greater_than_0(
        StockableInterface $stockable,
        StockInterface $stock
    ) {
        $this->beConstructedWith(false);

        $stock->isManageStock()->willReturn(true);
        $stock->hasAllowBackorders()->willReturn(false);      
        $stock->getMinStockLevel()->willReturn(0);          
        $stock->getOnHand()->willReturn(5);
        $stock->getOnHold()->willReturn(0);
        $stockable->getStock()->willReturn($stock);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_not_available_if_on_hold_quantity_is_same_as_on_hand(
        StockableInterface $stockable,
        StockInterface $stock        
    ) {
        $this->beConstructedWith(false);

        $stock->isManageStock()->willReturn(true);
        $stock->hasAllowBackorders()->willReturn(false);      
        $stock->getMinStockLevel()->willReturn(0);          
        $stock->getOnHand()->willReturn(5);
        $stock->getOnHold()->willReturn(5);
        $stockable->getStock()->willReturn($stock);

        $this->isStockAvailable($stockable)->shouldReturn(false);
    }

    function it_recognizes_stockable_as_available_if_on_hold_quantity_is_less_then_on_hand(
        StockableInterface $stockable,
        StockInterface $stock                
    ) {
        $this->beConstructedWith(false);

        $stock->isManageStock()->willReturn(true);
        $stock->hasAllowBackorders()->willReturn(false);      
        $stock->getMinStockLevel()->willReturn(0);          
        $stock->getOnHand()->willReturn(5);
        $stock->getOnHold()->willReturn(4);
        $stockable->getStock()->willReturn($stock);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_available_even_if_hand_quantity_is_lesser_than_or_equal_to_0_when_backorders_are_enabled(
        StockableInterface $stockable,
        StockInterface $stock                
    ) {
        $this->beConstructedWith(true);

        $stockable->getStock()->willReturn($stock);
        $stock->isManageStock()->willReturn(false);
        $stock->hasAllowBackorders()->willReturn(false);      
        $stock->getMinStockLevel()->willReturn(0);          

        $stock->getOnHand()->willReturn(0);
        $this->isStockAvailable($stockable)->shouldReturn(true);

        $stock->getOnHand()->willReturn(-5);
        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_not_available_if_on_hand_quantity_is_lesser_than_or_equal_to_0(
        StockableInterface $stockable,
        StockInterface $stock
    ) {
        $this->beConstructedWith(false);

        $stockable->getStock()->willReturn($stock);
        $stock->isManageStock()->willReturn(true);
        $stock->hasAllowBackorders()->willReturn(false);      
        $stock->getMinStockLevel()->willReturn(0);          
        $stock->getOnHand()->willReturn(0);
        $stock->getOnHold()->willReturn(0);

        $this->isStockAvailable($stockable)->shouldReturn(false);

        $stock->getOnHand()->willReturn(-5);
        $this->isStockAvailable($stockable)->shouldReturn(false);
    }

    function it_recognizes_stockable_as_not_available_if_variant_was_deleted(
        FakeStockableInterface $stockable,
        StockInterface $stock
    ) {
        $this->beConstructedWith(false);

        $stockable->isDeleted()->willReturn(true);
        $stockable->getStock()->willReturn($stock);
        $stock->isManageStock()->shouldNotBeCalled();

        $this->isStockAvailable($stockable)->shouldReturn(false);
    }

    function it_recognizes_any_stockable_and_quantity_as_sufficient_if_backorders_are_enabled(
        StockableInterface $stockable,
        StockInterface $stock        
    ) {
        $this->beConstructedWith(true);
        
        $stockable->getStock()->willReturn($stock);
        $stock->isManageStock()->willReturn(false);

        $this->isStockConvertable($stockable, 999)->shouldReturn(true);
    }

    function it_recognizes_stockable_stock_sufficient_if_on_hand_quantity_is_greater_than_required_quantity(
        StockableInterface $stockable,
        StockInterface $stock                
    ) {
        $this->beConstructedWith(false);

        $stockable->getStock()->willReturn($stock);
        $stock->isManageStock()->willReturn(true);
        $stock->hasAllowBackorders()->willReturn(false);   
        $stock->getMinStockLevel()->willReturn(0);          
        $stock->getMinQuantityInCart()->willReturn(null);
        $stock->getMaxQuantityInCart()->willReturn(null);
        $stock->getOnHand()->willReturn(10);
        $stock->getOnHold()->willReturn(0);

        $this->isStockConvertable($stockable, 5)->shouldReturn(true);

        $stock->getOnHand()->willReturn(15);
        $this->isStockConvertable($stockable, 15)->shouldReturn(true);
    }

    function it_recognizes_stock_sufficient_if_its_available_manage_stock_and_backorders_are_disabled(
        StockableInterface $stockable,
        StockInterface $stock                        
    ) {
        $this->beConstructedWith(false);

        $stockable->getStock()->willReturn($stock);
        $stock->isManageStock()->willReturn(false);
        $stock->getOnHand()->willReturn(0);

        $this->isStockConvertable($stockable, 999)->shouldReturn(true);

        $stock->getOnHand()->willReturn(-5);
        $this->isStockConvertable($stockable, 3)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_not_sufficient_if_variant_was_deleted(
        FakeStockableInterface $stockable,
        StockInterface $stock                                
    ) {
        $this->beConstructedWith(false);

        $stockable->isDeleted()->willReturn(true);

        $stockable->getStock()->shouldNotBeCalled();

        $this->isStockConvertable($stockable, 3)->shouldReturn(false);
    }

    function it_recognizes_stockable_stock_insufficient_if_min_stock_level_is_not_met(
        StockableInterface $stockable,
        StockInterface $stock                
    ) {
        $this->beConstructedWith(false);

        $stockable->getStock()->willReturn($stock);
        $stock->isManageStock()->willReturn(true);
        $stock->hasAllowBackorders()->willReturn(false);   
        $stock->getMinQuantityInCart()->willReturn(null);
        $stock->getMaxQuantityInCart()->willReturn(null);
        $stock->getOnHold()->willReturn(0);

        $stock->getMinStockLevel()->willReturn(6);          

        $stock->getOnHand()->willReturn(10);
        $this->isStockConvertable($stockable, 5)->shouldReturn(false);
        $this->isStockConvertable($stockable, 4)->shouldReturn(true);
    }

    function it_recognizes_stockable_stock_insufficient_if_min_and_max_cart_quantity_is_not_met(
        StockableInterface $stockable,
        StockInterface $stock                
    ) {
        $this->beConstructedWith(false);

        $stockable->getStock()->willReturn($stock);
        $stockable->getInventoryName()->willReturn('Product Name');
        $stock->isManageStock()->willReturn(true);
        $stock->hasAllowBackorders()->willReturn(false);   
        $stock->getMinStockLevel()->willReturn(0);          
        $stock->getOnHold()->willReturn(0);
        $stock->getOnHand()->willReturn(100);

        $stock->getMinQuantityInCart()->willReturn(5);
        $stock->getMaxQuantityInCart()->willReturn(15);

        $this->shouldThrow('Sylius\Component\Inventory\Manager\MinimumInsufficientRequirementsException')->duringIsStockConvertable($stockable, 4);
        $this->isStockConvertable($stockable, 12)->shouldReturn(true);
        $this->shouldThrow('Sylius\Component\Inventory\Manager\MaximumInsufficientRequirementsException')->duringIsStockConvertable($stockable, 16);
    }

}

interface FakeStockableInterface extends StockableInterface, SoftDeletableInterface
{

}
