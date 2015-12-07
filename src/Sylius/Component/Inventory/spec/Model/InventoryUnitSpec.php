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
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Order\Model\AdjustableInterface;

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
        $this->shouldImplement('Sylius\Component\Inventory\Model\InventoryUnitInterface');
    }

    function it_is_adjustable()
    {
        $this->shouldImplement(AdjustableInterface::class);
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

    function it_returns_its_stockable_sku(StockableInterface $stockable)
    {
        $stockable->getSku()->willReturn('IPHONE5');
        $this->setStockable($stockable);

        $this->getSku()->shouldReturn('IPHONE5');
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

    function it_has_no_adjustments_by_default()
    {
        $this->getAdjustments()->shouldBeAnInstanceOf('Doctrine\Common\Collections\Collection');
        $this->getAdjustments()->shouldBeEmpty();
    }

    function it_allows_to_add_adjustments(
        AdjustmentInterface $adjustment
    ) {
        $this->addAdjustment($adjustment);
        $this->getAdjustments()->shouldHaveCount(1);
    }

    function it_allows_to_retrieve_adjustments_by_type(
        AdjustmentInterface $adjustment,
        AdjustmentInterface $differentTypeAdjustment
    ) {
        $adjustment->getType()->willreturn('type');
        $differentTypeAdjustment->getType()->willreturn('different_type');

        $adjustment->setAdjustable($this)->shouldBeCalled();
        $differentTypeAdjustment->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);
        $this->addAdjustment($differentTypeAdjustment);

        $this->getAdjustments('type')->shouldHaveCount(1);
        $this->getAdjustments('different_type')->shouldHaveCount(1);
    }

    function it_does_not_allow_to_add_same_adjustments(
        AdjustmentInterface $adjustment
    ) {
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->getAdjustments()->shouldHaveCount(0);
        $this->addAdjustment($adjustment);
        $this->getAdjustments()->shouldHaveCount(1);
        $this->addAdjustment($adjustment);
        $this->getAdjustments()->shouldHaveCount(1);
    }

    function it_allows_removing_adjustments(
        AdjustmentInterface $adjustment
    ) {
        $this->addAdjustment($adjustment);
        $this->getAdjustments()->shouldHaveCount(1);
        $this->removeAdjustment($adjustment);
        $this->getAdjustments()->shouldHaveCount(0);
    }

    function it_allows_to_know_amount_of_all_adjustments(
        AdjustmentInterface $adjustment,
        AdjustmentInterface $adjustment2
    ) {
        $adjustment->getAmount()->willReturn(100);
        $adjustment2->getAmount()->willReturn(300);

        $adjustment->setAdjustable($this)->shouldBeCalled();
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);
        $this->addAdjustment($adjustment2);

        $adjustment->setAdjustable($this)->shouldBeCalled();
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $this->getAdjustmentsTotal()->shouldReturn(400);
    }

    function it_allows_to_know_amount_of_adjustments_by_type(
        AdjustmentInterface $adjustment,
        AdjustmentInterface $adjustment2
    ) {
        $adjustment->getAmount()->willReturn(100);
        $adjustment2->getAmount()->willReturn(300);
        $adjustment->getType()->willReturn('one');
        $adjustment2->getType()->willReturn('two');
        $adjustment->setAdjustable($this)->shouldBeCalled();
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $this->addAdjustment($adjustment);
        $this->addAdjustment($adjustment2);

        $this->getAdjustmentsTotal('one')->shouldReturn(100);
        $this->getAdjustmentsTotal('two')->shouldReturn(300);
    }

    function it_allows_to_remove_adjustments_by_type(
        AdjustmentInterface $adjustment,
        AdjustmentInterface $adjustment2
    ) {
        $this->addAdjustment($adjustment);
        $this->addAdjustment($adjustment2);
        $adjustment->getType()->willReturn('one');
        $adjustment2->getType()->willReturn('two');
        $adjustment->isLocked()->willReturn(false);
        $adjustment2->isLocked()->willReturn(false);

        $this->getAdjustments()->shouldHaveCount(2);
        $this->removeAdjustments('one');
        $this->getAdjustments()->shouldHaveCount(1);
        $this->removeAdjustments('two');
        $this->getAdjustments()->shouldHaveCount(0);
    }

    function it_doesnt_remove_adjustment_if_these_are_locked(
        AdjustmentInterface $adjustment,
        AdjustmentInterface $adjustment2
    ) {
        $this->addAdjustment($adjustment);
        $this->addAdjustment($adjustment2);

        $adjustment->isLocked()->willReturn(false);
        $adjustment2->isLocked()->willReturn(true);

        $adjustment->getType()->willReturn('type');
        $adjustment2->getType()->willReturn('type');

        $this->getAdjustments()->shouldHaveCount(2);

        $this->removeAdjustments('type');
        $this->getAdjustments()->shouldHaveCount(1);
    }

    function it_allows_to_clear_its_adjustments(
        AdjustmentInterface $adjustment,
        AdjustmentInterface $adjustment2
    ) {
        $this->addAdjustment($adjustment);
        $this->addAdjustment($adjustment2);

        $this->clearAdjustments();

        $this->getAdjustments()->shouldHaveCount(0);
    }
}
