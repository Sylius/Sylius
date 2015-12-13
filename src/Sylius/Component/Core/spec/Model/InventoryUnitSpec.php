<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

class InventoryUnitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\InventoryUnit');
    }

    function it_implements_Sylius_core_inventory_unit_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\Model\InventoryUnitInterface');
    }

    function it_is_adjustable()
    {
        $this->shouldImplement(AdjustableInterface::class);
    }

    function it_extends_Sylius_inventory_unit_model()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Model\InventoryUnit');
    }

    function it_implements_Sylius_shipment_item_interface()
    {
        $this->shouldImplement('Sylius\Component\Shipping\Model\ShipmentItemInterface');
    }

    function it_does_not_belong_to_any_shipment_by_default()
    {
        $this->getShipment()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_a_shipment(ShipmentInterface $shipment)
    {
        $this->setShipment($shipment);
        $this->getShipment()->shouldReturn($shipment);
    }

    function it_allows_detaching_itself_from_a_shipment(ShipmentInterface $shipment)
    {
        $this->setShipment($shipment);
        $this->getShipment()->shouldReturn($shipment);

        $this->setShipment(null);
        $this->getShipment()->shouldReturn(null);
    }

    function it_has_checkout_shipping_state_by_default()
    {
        $this->getShippingState()->shouldReturn(ShipmentInterface::STATE_CHECKOUT);
    }

    function its_shipping_state_is_mutable()
    {
        $this->setShippingState(ShipmentInterface::STATE_SHIPPED);
        $this->getShippingState()->shouldReturn(ShipmentInterface::STATE_SHIPPED);
    }

    function it_does_not_belong_to_an_order_item_by_default()
    {
        $this->getOrderItem()->shouldReturn(null);
    }

    function it_allows_attaching_itself_to_an_order_item(OrderItemInterface $order_item)
    {
        $this->setOrderItem($order_item);
        $this->getOrderItem()->shouldReturn($order_item);
    }

    function it_allows_detaching_itself_from_an_order_item(OrderItemInterface $order_item)
    {
        $this->setOrderItem($order_item);
        $this->getOrderItem()->shouldReturn($order_item);

        $this->setOrderItem(null);
        $this->getOrderItem()->shouldReturn(null);
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
