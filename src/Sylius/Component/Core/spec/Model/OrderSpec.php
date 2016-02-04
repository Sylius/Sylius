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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderShippingStates;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Order\Model\Order;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\User\Model\CustomerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Order');
    }

    function it_should_implement_Sylius_order_interface()
    {
        $this->shouldImplement(OrderInterface::class);
    }

    function it_should_extend_Sylius_order_mapped_superclass()
    {
        $this->shouldHaveType(Order::class);
    }

    function it_should_not_have_customer_defined_by_default()
    {
        $this->getCustomer()->shouldReturn(null);
    }

    function it_should_allow_defining_customer(CustomerInterface $customer)
    {
        $this->setCustomer($customer);
        $this->getCustomer()->shouldReturn($customer);
    }

    function its_channel_is_mutable(ChannelInterface $channel)
    {
        $this->setChannel($channel);
        $this->getChannel()->shouldReturn($channel);
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

    function its_checkout_state_is_mutable()
    {
        $this->setCheckoutState(OrderCheckoutStates::STATE_CART);
        $this->getCheckoutState()->shouldReturn(OrderCheckoutStates::STATE_CART);
    }

    function its_payment_state_is_mutable()
    {
        $this->setPaymentState(PaymentInterface::STATE_COMPLETED);
        $this->getPaymentState()->shouldReturn(PaymentInterface::STATE_COMPLETED);
    }

    function it_should_initialize_item_units_collection_by_default()
    {
        $this->getItemUnits()->shouldHaveType(Collection::class);
    }

    function it_should_initialize_shipments_collection_by_default()
    {
        $this->getShipments()->shouldHaveType(Collection::class);
    }

    function it_should_add_shipment_properly(ShipmentInterface $shipment)
    {
        $this->shouldNotHaveShipment($shipment);

        $shipment->setOrder($this)->shouldBeCalled();
        $this->addShipment($shipment);

        $this->shouldHaveShipment($shipment);
    }

    function it_should_remove_shipment_properly(ShipmentInterface $shipment)
    {
        $shipment->setOrder($this)->shouldBeCalled();
        $this->addShipment($shipment);

        $this->shouldHaveShipment($shipment);

        $shipment->setOrder(null)->shouldBeCalled();
        $this->removeShipment($shipment);

        $this->shouldNotHaveShipment($shipment);
    }

    function it_should_return_shipping_adjustments(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ) {
        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingAdjustment->isNeutral()->willReturn(true);

        $taxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustment->setAdjustable($this)->shouldBeCalled();
        $taxAdjustment->isNeutral()->willReturn(true);

        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($taxAdjustment);

        $this->getAdjustments()->count()->shouldReturn(2); //both adjustments have been added

        $shippingAdjustments = $this->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustments->count()->shouldReturn(1); //but here we only get shipping
        $shippingAdjustments->first()->shouldReturn($shippingAdjustment);
    }

    function it_should_remove_shipping_adjustments(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ) {
        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingAdjustment->isNeutral()->willReturn(true);

        $taxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustment->setAdjustable($this)->shouldBeCalled();
        $taxAdjustment->isNeutral()->willReturn(true);

        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($taxAdjustment);

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
        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingAdjustment->isNeutral()->willReturn(true);

        $taxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustment->setAdjustable($this)->shouldBeCalled();
        $taxAdjustment->isNeutral()->willReturn(true);

        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($taxAdjustment);

        $this->getAdjustments()->count()->shouldReturn(2); //both adjustments have been added

        $taxAdjustments = $this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustments->count()->shouldReturn(1); //but here we only get tax
        $taxAdjustments->first()->shouldReturn($taxAdjustment);
    }

    function it_should_remove_tax_adjustments(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ) {
        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingAdjustment->isNeutral()->willReturn(true);

        $taxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustment->setAdjustable($this)->shouldBeCalled();
        $taxAdjustment->isNeutral()->willReturn(true);

        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($taxAdjustment);

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

    function it_has_default_exchange_rate_equal_to_1()
    {
        $this->getExchangeRate()->shouldReturn(1.0);
    }

    function its_exchange_rate_is_mutable()
    {
        $this->setExchangeRate(1.25);
        $this->getExchangeRate()->shouldReturn(1.25);
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

        $item->getUnits()->willReturn([$unit1, $unit2]);
        $item->getTotal()->willReturn(4000);

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

        $item->getUnits()->willReturn([$unit1, $unit2]);
        $item->getTotal()->willReturn(4000);

        $item->setOrder($this)->shouldBeCalled();
        $this->addItem($item);

        $this->shouldNotBeBackorder();
    }

    function it_adds_and_removes_payments(PaymentInterface $payment)
    {
        $payment->getState()->willReturn(PaymentInterface::STATE_PENDING);
        $payment->setOrder($this)->shouldBeCalled();

        $this->addPayment($payment);
        $this->shouldHavePayment($payment);

        $payment->setOrder(null)->shouldBeCalled();

        $this->removePayment($payment);
        $this->shouldNotHavePayment($payment);
    }

    function it_returns_last_payment(PaymentInterface $payment1, PaymentInterface $payment2)
    {
        $payment1->getState()->willReturn(PaymentInterface::STATE_NEW);
        $payment1->setOrder($this)->shouldBeCalled();
        $payment2->getState()->willReturn(PaymentInterface::STATE_NEW);
        $payment2->setOrder($this)->shouldBeCalled();

        $this->addPayment($payment1);
        $this->addPayment($payment2);

        $this->getLastPayment()->shouldReturn($payment2);
    }

    function it_adds_and_removes_shipments(ShipmentInterface $shipment)
    {
        $shipment->setOrder($this)->shouldBeCalled();

        $this->addShipment($shipment);
        $this->shouldHaveShipment($shipment);

        $shipment->setOrder(null)->shouldBeCalled();

        $this->removeShipment($shipment);
        $this->shouldNotHaveShipment($shipment);
    }

    function it_adds_and_removes_promotion_coupons(CouponInterface $coupon)
    {
        $this->addPromotionCoupon($coupon);
        $this->shouldHavePromotionCoupon($coupon);

        $this->removePromotionCoupon($coupon);
        $this->shouldNotHavePromotionCoupon($coupon);
    }

    function it_count_promotions_subjects(OrderItemInterface $item1, OrderItemInterface $item2)
    {
        $this->addItem($item1);
        $this->addItem($item2);

        $this->getPromotionSubjectCount()->shouldReturn(2);
    }

    function it_adds_and_removes_promotions(PromotionInterface $promotion)
    {
        $this->addPromotion($promotion);
        $this->shouldHavePromotion($promotion);

        $this->removePromotion($promotion);
        $this->shouldNotHavePromotion($promotion);
    }
}
