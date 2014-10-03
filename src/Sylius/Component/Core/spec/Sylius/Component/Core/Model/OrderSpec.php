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
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderShippingStates;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\UserInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Order');
    }

    function it_should_implement_Sylius_order_interface()
    {
        $this->shouldImplement('Sylius\Component\Order\Model\OrderInterface');
    }

    function it_should_extend_Sylius_order_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Component\Order\Model\Order');
    }

    function it_should_not_have_user_defined_by_default()
    {
        $this->getUser()->shouldReturn(null);
    }

    function it_should_allow_defining_user(UserInterface $user)
    {
        $this->setUser($user);
        $this->getUser()->shouldReturn($user);
    }

    function it_should_not_have_shipping_address_by_default()
    {
        $this->getShippingAddress()->shouldReturn(null);
    }

    function it_should_allow_defining_shipping_address(AddressInterface $address)
    {
        $this->setShippingAddress($address);
        $this->getShippingAddress()->shouldReturn($address);
    }

    function it_should_not_have_billing_address_by_default()
    {
        $this->getBillingAddress()->shouldReturn(null);
    }

    function it_should_allow_defining_billing_address(AddressInterface $address)
    {
        $this->setBillingAddress($address);
        $this->getBillingAddress()->shouldReturn($address);
    }

    function it_should_initialize_inventory_units_collection_by_default()
    {
        $this->getInventoryUnits()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_should_initialize_shipments_collection_by_default()
    {
        $this->getShipments()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_should_add_shipment_properly(ShipmentInterface $shipment)
    {
        $this->hasShipment($shipment)->shouldReturn(false);

        $shipment->setOrder($this)->shouldBeCalled();
        $this->addShipment($shipment);

        $this->hasShipment($shipment)->shouldReturn(true);
    }

    function it_should_remove_shipment_properly(ShipmentInterface $shipment)
    {
        $shipment->setOrder($this)->shouldBeCalled();
        $this->addShipment($shipment);

        $this->hasShipment($shipment)->shouldReturn(true);

        $shipment->setOrder(null)->shouldBeCalled();
        $this->removeShipment($shipment);

        $this->hasShipment($shipment)->shouldReturn(false);
    }

    function it_should_return_shipping_adjustments(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ) {
        $this->addShippingAndTaxAdjustments($this, $shippingAdjustment, $taxAdjustment);

        $this->getAdjustments()->count()->shouldReturn(2); //both adjustments have been added

        $shippingAdjustments = $this->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustments->count()->shouldReturn(1); //but here we only get shipping
        $shippingAdjustments->first()->shouldReturn($shippingAdjustment);
    }

    function it_should_remove_shipping_adjustments(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ) {
        $this->addShippingAndTaxAdjustments($this, $shippingAdjustment, $taxAdjustment);

        $this->getAdjustments()->count()->shouldReturn(2); //both adjustments have been added

        $shippingAdjustment->isLocked()->willReturn(false);
        $shippingAdjustment->setAdjustable(null)->shouldBeCalled();
        $this->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        $this->getAdjustments()->count()->shouldReturn(1); //one has been removed
        $this->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->count()->shouldReturn(0); //shipping adjustment has been removed
    }

    function it_should_return_tax_adjustments(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ) {
        $this->addShippingAndTaxAdjustments($this, $shippingAdjustment, $taxAdjustment);

        $this->getAdjustments()->count()->shouldReturn(2); //both adjustments have been added

        $taxAdjustments = $this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustments->count()->shouldReturn(1); //but here we only get tax
        $taxAdjustments->first()->shouldReturn($taxAdjustment);
    }

    function it_should_remove_tax_adjustments(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ) {
        $this->addShippingAndTaxAdjustments($this, $shippingAdjustment, $taxAdjustment);

        $this->getAdjustments()->count()->shouldReturn(2); //both adjustments have been added

        $taxAdjustment->isLocked()->willReturn(false);
        $taxAdjustment->setAdjustable(null)->shouldBeCalled();
        $this->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

        $this->getAdjustments()->count()->shouldReturn(1); //one has been removed
        $this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->count()->shouldReturn(0); //tax adjustment has been removed
    }

    function it_should_not_have_currency_defined_by_default()
    {
        $this->getCurrency()->shouldReturn(null);
    }

    function it_should_allow_defining_currency()
    {
        $this->setCurrency('PLN');
        $this->getCurrency()->shouldReturn('PLN');
    }

    function it_has_checkout_shipping_state_by_default()
    {
        $this->getShippingState()->shouldReturn(OrderShippingStates::CHECKOUT);
    }

    function its_shipping_state_is_mutable()
    {
        $this->setShippingState(OrderShippingStates::SHIPPED);
        $this->getShippingState()->shouldReturn(OrderShippingStates::SHIPPED);
    }

    function it_is_a_backorder_if_contains_at_least_one_backordered_unit(
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2,
        OrderItemInterface $item
    ) {
        $unit1->getInventoryState()->willReturn(InventoryUnitInterface::STATE_BACKORDERED);
        $unit2->getInventoryState()->willReturn(InventoryUnitInterface::STATE_SOLD);

        $item->getInventoryUnits()->willReturn(array($unit1, $unit2));

        $item->setOrder($this)->shouldBeCalled();
        $this->addItem($item);

        $this->shouldBeBackorder();
    }

    function it_not_a_backorder_if_contains_no_backordered_units(
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2,
        OrderItemInterface $item
    ) {
        $unit1->getInventoryState()->willReturn(InventoryUnitInterface::STATE_SOLD);
        $unit2->getInventoryState()->willReturn(InventoryUnitInterface::STATE_SOLD);

        $item->getInventoryUnits()->willReturn(array($unit1, $unit2));

        $item->setOrder($this)->shouldBeCalled();
        $this->addItem($item);

        $this->shouldNotBeBackorder();
    }

    function it_should_allow_defining_email_from_user(UserInterface $user)
    {
        $user->getEmail()->willReturn('example@example.com');
        $this->setUser($user);
        $this->getEmail()->shouldReturn('example@example.com');
    }

    /**
     * Helper method
     *
     * @param OrderInterface      $order
     * @param AdjustmentInterface $shippingAdjustment
     * @param AdjustmentInterface $taxAdjustment
     */
    protected function addShippingAndTaxAdjustments(
        OrderInterface $order,
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ) {
        $shippingAdjustment->getLabel()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->setAdjustable($order)->shouldBeCalled();
        $taxAdjustment->getLabel()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustment->setAdjustable($order)->shouldBeCalled();

        $order->addAdjustment($shippingAdjustment);
        $order->addAdjustment($taxAdjustment);
    }
}
