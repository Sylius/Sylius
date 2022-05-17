<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Order\Model\Order as BaseOrder;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;

final class OrderSpec extends ObjectBehavior
{
    function it_implements_an_order_interface(): void
    {
        $this->shouldImplement(OrderInterface::class);
    }

    function it_extends_an_order(): void
    {
        $this->shouldHaveType(BaseOrder::class);
    }

    function it_does_not_have_a_customer_defined_by_default(): void
    {
        $this->getCustomer()->shouldReturn(null);
    }

    function it_allows_defining_customer(CustomerInterface $customer): void
    {
        $this->setCustomer($customer);
        $this->getCustomer()->shouldReturn($customer);
    }

    function it_allows_defining_authorized_customer(CustomerInterface $customer): void
    {
        $this->setCustomerWithAuthorization($customer);
        $this->getCustomer()->shouldReturn($customer);
        $this->isCreatedByGuest()->shouldReturn(false);
    }

    function its_created_by_guest_customer_by_default(): void
    {
        $this->isCreatedByGuest()->shouldReturn(true);
    }

    function it_allows_to_mutate_create_by_guest_field(): void
    {
        $this->setCreatedByGuest(false);
        $this->isCreatedByGuest()->shouldReturn(false);
    }

    function its_customer_can_be_nullable(): void
    {
        $this->setCustomer(null);
        $this->getCustomer()->shouldReturn(null);
    }

    function its_channel_is_mutable(ChannelInterface $channel): void
    {
        $this->setChannel($channel);
        $this->getChannel()->shouldReturn($channel);
    }

    function it_does_not_have_shipping_address_by_default(): void
    {
        $this->getShippingAddress()->shouldReturn(null);
    }

    function it_allows_defining_shipping_address(AddressInterface $address): void
    {
        $this->setShippingAddress($address);
        $this->getShippingAddress()->shouldReturn($address);
    }

    function it_does_not_have_billing_address_by_default(): void
    {
        $this->getBillingAddress()->shouldReturn(null);
    }

    function it_allows_defining_billing_address(AddressInterface $address): void
    {
        $this->setBillingAddress($address);
        $this->getBillingAddress()->shouldReturn($address);
    }

    function its_checkout_state_is_mutable(): void
    {
        $this->setCheckoutState(OrderCheckoutStates::STATE_CART);
        $this->getCheckoutState()->shouldReturn(OrderCheckoutStates::STATE_CART);
    }

    function its_payment_state_is_mutable(): void
    {
        $this->setPaymentState(PaymentInterface::STATE_COMPLETED);
        $this->getPaymentState()->shouldReturn(PaymentInterface::STATE_COMPLETED);
    }

    function it_initializes_item_units_collection_by_default(): void
    {
        $this->getItemUnits()->shouldHaveType(Collection::class);
    }

    function it_initializes_shipments_collection_by_default(): void
    {
        $this->getShipments()->shouldHaveType(Collection::class);
    }

    function it_adds_shipment_properly(ShipmentInterface $shipment): void
    {
        $this->shouldNotHaveShipment($shipment);

        $shipment->setOrder($this)->shouldBeCalled();
        $this->addShipment($shipment);

        $this->shouldHaveShipment($shipment);
    }

    function it_removes_a_shipment_properly(ShipmentInterface $shipment): void
    {
        $shipment->setOrder($this)->shouldBeCalled();
        $this->addShipment($shipment);

        $this->shouldHaveShipment($shipment);

        $shipment->setOrder(null)->shouldBeCalled();
        $this->removeShipment($shipment);

        $this->shouldNotHaveShipment($shipment);
    }

    function it_removes_shipments(ShipmentInterface $shipment): void
    {
        $this->addShipment($shipment);
        $this->hasShipment($shipment)->shouldReturn(true);

        $this->removeShipments();

        $this->hasShipment($shipment)->shouldReturn(false);
    }

    function it_returns_shipping_adjustments(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ): void {
        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingAdjustment->isNeutral()->willReturn(true);

        $taxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustment->setAdjustable($this)->shouldBeCalled();
        $taxAdjustment->isNeutral()->willReturn(true);

        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($taxAdjustment);

        $this->getAdjustments()->count()->shouldReturn(2);

        $shippingAdjustments = $this->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustments->count()->shouldReturn(1);
        $shippingAdjustments->first()->shouldReturn($shippingAdjustment);
    }

    function it_removes_shipping_adjustments(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ): void {
        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingAdjustment->isNeutral()->willReturn(true);

        $taxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustment->setAdjustable($this)->shouldBeCalled();
        $taxAdjustment->isNeutral()->willReturn(true);

        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($taxAdjustment);

        $this->getAdjustments()->count()->shouldReturn(2);

        $shippingAdjustment->isLocked()->willReturn(false);
        $shippingAdjustment->setAdjustable(null)->shouldBeCalled();
        $this->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        $this->getAdjustments()->count()->shouldReturn(1);
        $this->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->count()->shouldReturn(0);
    }

    function it_returns_tax_adjustments(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ): void {
        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingAdjustment->isNeutral()->willReturn(true);

        $taxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustment->setAdjustable($this)->shouldBeCalled();
        $taxAdjustment->isNeutral()->willReturn(true);

        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($taxAdjustment);

        $this->getAdjustments()->count()->shouldReturn(2);

        $taxAdjustments = $this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustments->count()->shouldReturn(1);
        $taxAdjustments->first()->shouldReturn($taxAdjustment);
    }

    function it_removes_tax_adjustments(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $taxAdjustment
    ): void {
        $shippingAdjustment->getType()->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingAdjustment->isNeutral()->willReturn(true);

        $taxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $taxAdjustment->setAdjustable($this)->shouldBeCalled();
        $taxAdjustment->isNeutral()->willReturn(true);

        $this->addAdjustment($shippingAdjustment);
        $this->addAdjustment($taxAdjustment);

        $this->getAdjustments()->count()->shouldReturn(2);

        $taxAdjustment->isLocked()->willReturn(false);
        $taxAdjustment->setAdjustable(null)->shouldBeCalled();
        $this->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

        $this->getAdjustments()->count()->shouldReturn(1);
        $this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->count()->shouldReturn(0);
    }

    function it_does_not_have_a_currency_code_defined_by_default(): void
    {
        $this->getCurrencyCode()->shouldReturn(null);
    }

    function it_allows_defining_a_currency_code(): void
    {
        $this->setCurrencyCode('PLN');
        $this->getCurrencyCode()->shouldReturn('PLN');
    }

    function it_has_no_default_locale_code(): void
    {
        $this->getLocaleCode()->shouldReturn(null);
    }

    function its_locale_code_is_mutable(): void
    {
        $this->setLocaleCode('en');
        $this->getLocaleCode()->shouldReturn('en');
    }

    function it_has_a_cart_shipping_state_by_default(): void
    {
        $this->getShippingState()->shouldReturn(OrderShippingStates::STATE_CART);
    }

    function its_shipping_state_is_mutable(): void
    {
        $this->setShippingState(OrderShippingStates::STATE_SHIPPED);
        $this->getShippingState()->shouldReturn(OrderShippingStates::STATE_SHIPPED);
    }

    function it_adds_and_removes_payments(PaymentInterface $payment): void
    {
        $payment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $payment->setOrder($this)->shouldBeCalled();

        $this->addPayment($payment);
        $this->shouldHavePayment($payment);

        $payment->setOrder(null)->shouldBeCalled();

        $this->removePayment($payment);
        $this->shouldNotHavePayment($payment);
    }

    function it_returns_last_payment_with_given_state(
        PaymentInterface $payment1,
        PaymentInterface $payment2,
        PaymentInterface $payment3,
        PaymentInterface $payment4
    ): void {
        $payment1->getState()->willReturn(PaymentInterface::STATE_CART);
        $payment1->setOrder($this)->shouldBeCalled();

        $payment2->getState()->willReturn(PaymentInterface::STATE_CANCELLED);
        $payment2->setOrder($this)->shouldBeCalled();

        $payment3->getState()->willReturn(PaymentInterface::STATE_PROCESSING);
        $payment3->setOrder($this)->shouldBeCalled();

        $payment4->getState()->willReturn(PaymentInterface::STATE_FAILED);
        $payment4->setOrder($this)->shouldBeCalled();

        $this->addPayment($payment1);
        $this->addPayment($payment2);
        $this->addPayment($payment3);
        $this->addPayment($payment4);

        $this->getLastPayment(OrderInterface::STATE_CART)->shouldReturn($payment1);
    }

    function it_returns_a_null_if_there_is_no_payments_after_trying_to_get_last_payment(): void
    {
        $this->getLastPayment(OrderInterface::STATE_CART)->shouldReturn(null);
    }

    function it_returns_last_payment_with_any_state_if_there_is_no_target_state_specified(
        PaymentInterface $payment1,
        PaymentInterface $payment2,
        PaymentInterface $payment3,
        PaymentInterface $payment4
    ): void {
        $payment1->getState()->willReturn(PaymentInterface::STATE_CART);
        $payment1->setOrder($this)->shouldBeCalled();

        $payment2->getState()->willReturn(PaymentInterface::STATE_CANCELLED);
        $payment2->setOrder($this)->shouldBeCalled();

        $payment3->getState()->willReturn(PaymentInterface::STATE_PROCESSING);
        $payment3->setOrder($this)->shouldBeCalled();

        $payment4->getState()->willReturn(PaymentInterface::STATE_FAILED);
        $payment4->setOrder($this)->shouldBeCalled();

        $this->addPayment($payment1);
        $this->addPayment($payment2);
        $this->addPayment($payment3);
        $this->addPayment($payment4);

        $this->getLastPayment()->shouldReturn($payment4);
    }

    function it_adds_and_removes_shipments(ShipmentInterface $shipment): void
    {
        $shipment->setOrder($this)->shouldBeCalled();

        $this->addShipment($shipment);
        $this->shouldHaveShipment($shipment);

        $shipment->setOrder(null)->shouldBeCalled();

        $this->removeShipment($shipment);
        $this->shouldNotHaveShipment($shipment);
    }

    function it_has_a_promotion_coupon(PromotionCouponInterface $coupon): void
    {
        $this->setPromotionCoupon($coupon);
        $this->getPromotionCoupon()->shouldReturn($coupon);
    }

    function it_counts_promotions_subjects(OrderItemInterface $item1, OrderItemInterface $item2): void
    {
        $item1->getQuantity()->willReturn(4);
        $item1->getTotal()->willReturn(420);
        $item1->setOrder($this)->will(function () {});

        $item2->getQuantity()->willReturn(3);
        $item2->getTotal()->willReturn(666);
        $item2->setOrder($this)->will(function () {});

        $this->addItem($item1);
        $this->addItem($item2);

        $this->getPromotionSubjectCount()->shouldReturn(7);
    }

    function it_adds_and_removes_promotions(PromotionInterface $promotion): void
    {
        $this->addPromotion($promotion);
        $this->shouldHavePromotion($promotion);

        $this->removePromotion($promotion);
        $this->shouldNotHavePromotion($promotion);
    }

    function it_returns_0_tax_total_when_there_are_no_items_and_adjustments(): void
    {
        $this->getTaxTotal()->shouldReturn(0);
    }

    function it_returns_a_tax_of_all_items_as_tax_total_when_there_are_no_tax_adjustments(
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2
    ): void {
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

    function it_returns_a_tax_of_all_items_and_non_neutral_shipping_tax_as_tax_total(
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment
    ): void {
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

    function it_returns_a_tax_of_all_items_and_neutral_shipping_tax_as_tax_total(
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment
    ): void {
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

    function it_includes_a_non_neutral_tax_adjustments_in_shipping_total(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment
    ): void {
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

    function it_returns_a_shipping_total_decreased_by_shipping_promotion(
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment,
        AdjustmentInterface $shippingPromotionAdjustment
    ): void {
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
    ): void {
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

    function it_returns_0_as_promotion_total_when_there_are_no_order_promotion_adjustments(): void
    {
        $this->getOrderPromotionTotal()->shouldReturn(0);
    }

    function it_returns_a_sum_of_all_order_promotion_adjustments_order_item_promotion_adjustments_and_order_unit_promotion_adjustments_applied_to_items_as_order_promotion_total(
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        AdjustmentInterface $orderAdjustment1,
        AdjustmentInterface $orderAdjustment2,
        AdjustmentInterface $orderItemAdjustment1,
        AdjustmentInterface $orderItemAdjustment2,
        AdjustmentInterface $orderUnitAdjustment1,
        AdjustmentInterface $orderUnitAdjustment2
    ): void {
        $orderAdjustment1->getType()->willReturn(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $orderAdjustment1->getAmount()->willReturn(-400);
        $orderAdjustment1->isNeutral()->willReturn(false);

        $orderAdjustment2->getType()->willReturn(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $orderAdjustment2->getAmount()->willReturn(-600);
        $orderAdjustment2->isNeutral()->willReturn(false);

        $orderItemAdjustment1->getType()->willReturn(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $orderItemAdjustment1->getAmount()->willReturn(-100);
        $orderItemAdjustment1->isNeutral()->willReturn(false);

        $orderItemAdjustment2->getType()->willReturn(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $orderItemAdjustment2->getAmount()->willReturn(-200);
        $orderItemAdjustment2->isNeutral()->willReturn(false);

        $orderUnitAdjustment1->getType()->willReturn(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $orderUnitAdjustment1->getAmount()->willReturn(-50);
        $orderUnitAdjustment1->isNeutral()->willReturn(false);

        $orderUnitAdjustment2->getType()->willReturn(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $orderUnitAdjustment2->getAmount()->willReturn(-20);
        $orderUnitAdjustment2->isNeutral()->willReturn(false);

        $orderItem1->getTotal()->willReturn(500);
        $orderItem2->getTotal()->willReturn(300);

        $orderItem1
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderAdjustment1->getWrappedObject()]))
        ;
        $orderItem2->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderAdjustment2->getWrappedObject()]))
        ;
        $orderItem1
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderItemAdjustment1->getWrappedObject()]))
        ;
        $orderItem2->getAdjustmentsRecursively(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderItemAdjustment2->getWrappedObject()]))
        ;
        $orderItem1
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderUnitAdjustment1->getWrappedObject()]))
        ;
        $orderItem2->getAdjustmentsRecursively(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderUnitAdjustment2->getWrappedObject()]))
        ;

        $orderItem1->setOrder($this)->shouldBeCalled();
        $orderItem2->setOrder($this)->shouldBeCalled();
        $this->addItem($orderItem1);
        $this->addItem($orderItem2);

        $this->getOrderPromotionTotal()->shouldReturn(-1370);
    }

    function it_does_not_include_a_shipping_promotion_adjustment_in_order_promotion_total(
        AdjustmentInterface $shippingPromotionAdjustment,
        AdjustmentInterface $orderAdjustment,
        AdjustmentInterface $orderItemAdjustment,
        AdjustmentInterface $orderUnitAdjustment,
        OrderItemInterface $orderItem
    ): void {
        $orderAdjustment->getType()->willReturn(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $orderAdjustment->getAmount()->willReturn(-400);
        $orderAdjustment->isNeutral()->willReturn(false);

        $orderItemAdjustment->getType()->willReturn(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $orderItemAdjustment->getAmount()->willReturn(-100);
        $orderItemAdjustment->isNeutral()->willReturn(false);

        $orderUnitAdjustment->getType()->willReturn(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $orderUnitAdjustment->getAmount()->willReturn(-50);
        $orderUnitAdjustment->isNeutral()->willReturn(false);

        $orderItem->getTotal()->willReturn(500);
        $orderItem
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderAdjustment->getWrappedObject()]))
        ;
        $orderItem
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderItemAdjustment->getWrappedObject()]))
        ;
        $orderItem
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderUnitAdjustment->getWrappedObject()]))
        ;

        $shippingPromotionAdjustment->getType()->willReturn(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $shippingPromotionAdjustment->isNeutral()->willReturn(false);
        $shippingPromotionAdjustment->getAmount()->willReturn(-100);

        $orderItem->setOrder($this)->shouldBeCalled();
        $this->addItem($orderItem);

        $shippingPromotionAdjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($shippingPromotionAdjustment);

        $this->getOrderPromotionTotal()->shouldReturn(-550);
    }

    function it_includes_order_promotion_adjustments_order_item_promotion_adjustments_and_order_unit_promotion_adjustments_in_order_promotion_total(
        OrderItemInterface $orderItem,
        AdjustmentInterface $orderAdjustmentForOrder,
        AdjustmentInterface $orderAdjustmentForItem,
        AdjustmentInterface $orderItemAdjustmentForOrder,
        AdjustmentInterface $orderItemAdjustmentForItem,
        AdjustmentInterface $orderUnitAdjustmentForOrder,
        AdjustmentInterface $orderUnitAdjustmentForItem
    ): void {
        $orderAdjustmentForOrder->getType()->willReturn(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $orderAdjustmentForOrder->getAmount()->willReturn(-120);
        $orderAdjustmentForOrder->isNeutral()->willReturn(false);
        $orderAdjustmentForOrder->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($orderAdjustmentForOrder);

        $orderAdjustmentForItem->getType()->willReturn(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $orderAdjustmentForItem->getAmount()->willReturn(-150);
        $orderAdjustmentForItem->isNeutral()->willReturn(false);

        $orderItemAdjustmentForOrder->getType()->willReturn(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $orderItemAdjustmentForOrder->getAmount()->willReturn(-230);
        $orderItemAdjustmentForOrder->isNeutral()->willReturn(false);
        $orderItemAdjustmentForOrder->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($orderItemAdjustmentForOrder);

        $orderItemAdjustmentForItem->getType()->willReturn(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $orderItemAdjustmentForItem->getAmount()->willReturn(-250);
        $orderItemAdjustmentForItem->isNeutral()->willReturn(false);

        $orderUnitAdjustmentForOrder->getType()->willReturn(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $orderUnitAdjustmentForOrder->getAmount()->willReturn(-53);
        $orderUnitAdjustmentForOrder->isNeutral()->willReturn(false);
        $orderUnitAdjustmentForOrder->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($orderUnitAdjustmentForOrder);

        $orderUnitAdjustmentForItem->getType()->willReturn(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $orderUnitAdjustmentForItem->getAmount()->willReturn(-20);
        $orderUnitAdjustmentForItem->isNeutral()->willReturn(false);

        $orderItem->getTotal()->willReturn(200);
        $orderItem
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderAdjustmentForItem->getWrappedObject()]))
        ;
        $orderItem
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderItemAdjustmentForItem->getWrappedObject()]))
        ;
        $orderItem
            ->getAdjustmentsRecursively(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$orderUnitAdjustmentForItem->getWrappedObject()]))
        ;
        $orderItem->setOrder($this)->shouldBeCalled();
        $this->addItem($orderItem);

        $this->getOrderPromotionTotal()->shouldReturn(-823);
    }

    function it_has_a_token_value(): void
    {
        $this->setTokenValue('xyzasdxqwe');

        $this->getTokenValue()->shouldReturn('xyzasdxqwe');
    }

    function it_has_customer_ip(): void
    {
        $this->setCustomerIp('172.16.254.1');
        $this->getCustomerIp()->shouldReturn('172.16.254.1');
    }

    function it_calculates_total_of_non_discounted_items(
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        ProductVariantInterface $variant1,
        ProductVariantInterface $variant2,
        ChannelInterface $channel,
        CatalogPromotionInterface $catalogPromotion
    ): void {
        $item1->getTotal()->willReturn(500);
        $item1->getVariant()->willReturn($variant1);
        $item2->getTotal()->willReturn(800);
        $item2->getVariant()->willReturn($variant2);

        $variant1->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());
        $variant2->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection([$catalogPromotion]));

        $item1->setOrder($this)->shouldBeCalled();
        $item2->setOrder($this)->shouldBeCalled();
        $this->addItem($item1);
        $this->addItem($item2);
        $this->setChannel($channel);

        $this->getNonDiscountedItemsTotal()->shouldReturn(500);
    }

    function it_returns_a_proper_total_of_taxes_included_in_price_or_excluded_from_it(
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        AdjustmentInterface $includedUnitTaxAdjustment,
        AdjustmentInterface $excludedUnitTaxAdjustment,
        AdjustmentInterface $shippingTaxAdjustment
    ): void {
        $includedUnitTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $includedUnitTaxAdjustment->isNeutral()->willReturn(true);
        $includedUnitTaxAdjustment->getAmount()->willReturn(1000);

        $excludedUnitTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $excludedUnitTaxAdjustment->isNeutral()->willReturn(false);
        $excludedUnitTaxAdjustment->getAmount()->willReturn(800);

        $firstOrderItem->getTotal()->willReturn(5000);
        $firstOrderItem
            ->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$includedUnitTaxAdjustment->getWrappedObject()]))
        ;

        $secondOrderItem->getTotal()->willReturn(5000);
        $secondOrderItem
            ->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$excludedUnitTaxAdjustment->getWrappedObject()]))
        ;

        $firstOrderItem->setOrder($this)->shouldBeCalled();
        $secondOrderItem->setOrder($this)->shouldBeCalled();
        $this->addItem($firstOrderItem);
        $this->addItem($secondOrderItem);

        $shippingTaxAdjustment->getType()->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $shippingTaxAdjustment->setAdjustable($this)->shouldBeCalled();
        $shippingTaxAdjustment->isNeutral()->willReturn(true);
        $shippingTaxAdjustment->getAmount()->willReturn(500);
        $this->addAdjustment($shippingTaxAdjustment);

        $this->getTaxIncludedTotal()->shouldReturn(1500);
        $this->getTaxExcludedTotal()->shouldReturn(800);
    }
}
