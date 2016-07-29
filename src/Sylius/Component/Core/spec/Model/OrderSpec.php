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
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderShippingStates;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Order\Model\Order as BaseOrder;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * @mixin Order
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class OrderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Order::class);
    }

    function it_should_implement_Sylius_order_interface()
    {
        $this->shouldImplement(OrderInterface::class);
    }

    function it_should_extend_Sylius_order_mapped_superclass()
    {
        $this->shouldHaveType(BaseOrder::class);
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

    function it_should_not_have_currency_code_defined_by_default()
    {
        $this->getCurrencyCode()->shouldReturn(null);
    }

    function it_should_allow_defining_currency_code()
    {
        $this->setCurrencyCode('PLN');
        $this->getCurrencyCode()->shouldReturn('PLN');
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

    function it_has_cart_shipping_state_by_default()
    {
        $this->getShippingState()->shouldReturn(OrderShippingStates::STATE_CART);
    }

    function its_shipping_state_is_mutable()
    {
        $this->setShippingState(OrderShippingStates::STATE_SHIPPED);
        $this->getShippingState()->shouldReturn(OrderShippingStates::STATE_SHIPPED);
    }

    function it_adds_and_removes_payments(PaymentInterface $payment)
    {
        $payment->getState()->willReturn(PaymentInterface::STATE_NEW);
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

    function it_has_promotion_coupon(CouponInterface $coupon)
    {
        $this->setPromotionCoupon($coupon);
        $this->getPromotionCoupon()->shouldReturn($coupon);
    }

    function it_count_promotions_subjects(OrderItemInterface $item1, OrderItemInterface $item2)
    {
        $this->addItem($item1);
        $item1->getQuantity()->willReturn(4);
        $this->addItem($item2);
        $item2->getQuantity()->willReturn(3);

        $this->getPromotionSubjectCount()->shouldReturn(7);
    }

    function it_adds_and_removes_promotions(PromotionInterface $promotion)
    {
        $this->addPromotion($promotion);
        $this->shouldHavePromotion($promotion);

        $this->removePromotion($promotion);
        $this->shouldNotHavePromotion($promotion);
    }

    function it_returns_0_tax_total_when_there_are_no_items_and_adjustments()
    {
        $this->getTaxTotal()->shouldReturn(0);
    }

    function it_returns_tax_of_all_items_as_tax_total_when_there_are_no_tax_adjustments(
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2
    ) {
        $orderItem1->getTotal()->willReturn(1100);
        $orderItem1->getTaxTotal()->willReturn(100);
        $orderItem2->getTotal()->willReturn(1050);
        $orderItem2->getTaxTotal()->willReturn(50);

        $orderItem1->setOrder($this)->shouldBeCalled();
        $orderItem2->setOrder($this)->shouldBeCalled();
        $this->addItem($orderItem1);
        $this->addItem($orderItem2);

        $this->getTaxTotal()->shouldReturn(150);
    }

    function it_returns_tax_of_all_items_and_non_neutral_shipping_tax_as_tax_total(
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment
    ) {
        $orderItem1->getTotal()->willReturn(1100);
        $orderItem1->getTaxTotal()->willReturn(100);
        $orderItem2->getTotal()->willReturn(1050);
        $orderItem2->getTaxTotal()->willReturn(50);

        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->isNeutral()->willReturn(false);
        $shippingAdjustment->getAmount()->willReturn(1000);
        $shippingTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $shippingTaxAdjustment->isNeutral()->willReturn(false);
        $shippingTaxAdjustment->getAmount()->willReturn(70);

        $orderItem1->setOrder($this)->shouldBeCalled();
        $orderItem2->setOrder($this)->shouldBeCalled();
        $this->addItem($orderItem1);
        $this->addItem($orderItem2);

        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($shippingAdjustment);

        $shippingTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($shippingTaxAdjustment);

        $this->getTaxTotal()->shouldReturn(220);
    }

    function it_returns_tax_of_all_items_and_neutral_shipping_tax_as_tax_total(
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment
    ) {
        $orderItem1->getTotal()->willReturn(1100);
        $orderItem1->getTaxTotal()->willReturn(100);
        $orderItem2->getTotal()->willReturn(1050);
        $orderItem2->getTaxTotal()->willReturn(50);

        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->isNeutral()->willReturn(false);
        $shippingAdjustment->getAmount()->willReturn(1000);

        $shippingTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $shippingTaxAdjustment->isNeutral()->willReturn(true);
        $shippingTaxAdjustment->getAmount()->willReturn(70);

        $orderItem1->setOrder($this)->shouldBeCalled();
        $orderItem2->setOrder($this)->shouldBeCalled();
        $this->addItem($orderItem1);
        $this->addItem($orderItem2);

        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($shippingTaxAdjustment);

        $this->getTaxTotal()->shouldReturn(220);
    }

    function it_includes_non_neutral_tax_adjustments_in_shipping_total(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment
    ) {
        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->isNeutral()->willReturn(false);
        $shippingAdjustment->getAmount()->willReturn(1000);

        $shippingTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $shippingTaxAdjustment->isNeutral()->willReturn(false);
        $shippingTaxAdjustment->getAmount()->willReturn(70);

        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($shippingTaxAdjustment);

        $this->getShippingTotal()->shouldReturn(1070);
    }

    function it_returns_shipping_total_decreased_by_shipping_promotion(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment,
        AdjustmentInterface $shippingPromotionAdjustment
    ) {
        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->isNeutral()->willReturn(false);
        $shippingAdjustment->getAmount()->willReturn(1000);

        $shippingTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $shippingTaxAdjustment->isNeutral()->willReturn(false);
        $shippingTaxAdjustment->getAmount()->willReturn(70);

        $shippingPromotionAdjustment->getType()->willReturn(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $shippingPromotionAdjustment->isNeutral()->willReturn(false);
        $shippingPromotionAdjustment->getAmount()->willReturn(-100);

        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingPromotionAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($shippingTaxAdjustment);
        $this->addAdjustment($shippingPromotionAdjustment);

        $this->getShippingTotal()->shouldReturn(970);
    }

    function it_does_not_include_neutral_tax_adjustments_in_shipping_total(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $neutralShippingTaxAdjustment
    ) {
        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->isNeutral()->willReturn(false);
        $shippingAdjustment->getAmount()->willReturn(1000);

        $neutralShippingTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $neutralShippingTaxAdjustment->isNeutral()->willReturn(true);
        $neutralShippingTaxAdjustment->getAmount()->willReturn(70);

        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $neutralShippingTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($neutralShippingTaxAdjustment);

        $this->getShippingTotal()->shouldReturn(1000);
    }

    function it_returns_0_as_promotion_total_when_there_are_no_order_promotion_adjustments()
    {
        $this->getOrderPromotionTotal()->shouldReturn(0);
    }

    function it_returns_sum_of_all_order_promotion_adjustments_applied_to_items_as_order_promotion_total(
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2
    ) {
        $orderItem1->getTotal()->willReturn(500);
        $orderItem2->getTotal()->willReturn(300);

        $orderItem1->getAdjustmentsTotalRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(-400);
        $orderItem2->getAdjustmentsTotalRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(-600);

        $orderItem1->setOrder($this)->shouldBeCalled();
        $orderItem2->setOrder($this)->shouldBeCalled();
        $this->addItem($orderItem1);
        $this->addItem($orderItem2);

        $this->getOrderPromotionTotal()->shouldReturn(-1000);
    }

    function it_does_not_include_shipping_promotion_adjustment_in_order_promotion_total(
        AdjustmentInterface $shippingPromotionAdjustment,
        OrderItemInterface $orderItem1
    ) {
        $orderItem1->getTotal()->willReturn(500);
        $orderItem1->getAdjustmentsTotalRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)->willReturn(-400);

        $shippingPromotionAdjustment->getType()->willReturn(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $shippingPromotionAdjustment->isNeutral()->willReturn(false);
        $shippingPromotionAdjustment->getAmount()->willReturn(-100);

        $orderItem1->setOrder($this)->shouldBeCalled();
        $this->addItem($orderItem1);

        $shippingPromotionAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($shippingPromotionAdjustment);

        $this->getOrderPromotionTotal()->shouldReturn(-400);
    }
}
