<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Order\Model\Order as BaseOrder;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;

final class OrderTest extends TestCase
{
    private CustomerInterface&MockObject $customer;

    private ChannelInterface&MockObject $channel;

    private AddressInterface&MockObject $address;

    private MockObject&ShipmentInterface $shipment;

    private MockObject&ShipmentUnitInterface $shipmentUnit;

    private AdjustmentInterface&MockObject $shippingAdjustment;

    private AdjustmentInterface&MockObject $taxAdjustment;

    private AdjustmentInterface&MockObject $shippingTaxAdjustment;

    private MockObject&PaymentInterface $firstPayment;

    private MockObject&PaymentInterface $secondPayment;

    private MockObject&PaymentInterface $thirdPayment;

    private MockObject&PaymentInterface $fourthPayment;

    private MockObject&OrderItemInterface $firstItem;

    private MockObject&OrderItemInterface $secondItem;

    private MockObject&PromotionInterface $promotion;

    private Order $order;

    protected function setUp(): void
    {
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->address = $this->createMock(AddressInterface::class);
        $this->shipment = $this->createMock(ShipmentInterface::class);
        $this->shipmentUnit = $this->createMock(ShipmentUnitInterface::class);
        $this->shippingAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->taxAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->shippingTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->firstPayment = $this->createMock(PaymentInterface::class);
        $this->secondPayment = $this->createMock(PaymentInterface::class);
        $this->thirdPayment = $this->createMock(PaymentInterface::class);
        $this->fourthPayment = $this->createMock(PaymentInterface::class);
        $this->firstItem = $this->createMock(OrderItemInterface::class);
        $this->secondItem = $this->createMock(OrderItemInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->order = new Order();
    }

    public function testShouldImplementOrderInterface(): void
    {
        $this->assertInstanceOf(OrderInterface::class, $this->order);
    }

    public function testShouldExtendOrder(): void
    {
        $this->assertInstanceOf(BaseOrder::class, $this->order);
    }

    public function testShouldNotHaveCustomerDefinedByDefault(): void
    {
        $this->assertNull($this->order->getCustomer());
    }

    public function testShouldAllowDefineCustomer(): void
    {
        $this->order->setCustomer($this->customer);

        $this->assertSame($this->customer, $this->order->getCustomer());
    }

    public function testShouldAllowDefineAuthorizedCustomer(): void
    {
        $this->order->setCustomerWithAuthorization($this->customer);

        $this->assertSame($this->customer, $this->order->getCustomer());
        $this->assertFalse($this->order->isCreatedByGuest());
    }

    public function testShouldCreatedByGuestBeTrueByDefault(): void
    {
        $this->assertTrue($this->order->isCreatedByGuest());
    }

    public function testShouldCustomerBeNullable(): void
    {
        $this->order->setCustomer(null);

        $this->assertNull($this->order->getCustomer());
    }

    public function testShouldChannelBeMutable(): void
    {
        $this->order->setChannel($this->channel);

        $this->assertSame($this->channel, $this->order->getChannel());
    }

    public function testShouldNotHaveShippingAddressByDefault(): void
    {
        $this->assertNull($this->order->getShippingAddress());
    }

    public function testShouldAllowDefiningShippingAddress(): void
    {
        $this->order->setShippingAddress($this->address);

        $this->assertSame($this->address, $this->order->getShippingAddress());
    }

    public function testShouldNotHaveBillingAddressByDefault(): void
    {
        $this->assertNull($this->order->getBillingAddress());
    }

    public function testShouldAllowDefiningBillingAddress(): void
    {
        $this->order->setBillingAddress($this->address);

        $this->assertSame($this->address, $this->order->getBillingAddress());
    }

    public function testShouldCheckoutStateBeMutable(): void
    {
        $this->order->setCheckoutState(OrderInterface::STATE_CART);

        $this->assertSame(OrderInterface::STATE_CART, $this->order->getCheckoutState());
    }

    public function testShouldPaymentStateBeMutable(): void
    {
        $this->order->setPaymentState(PaymentInterface::STATE_COMPLETED);

        $this->assertSame(PaymentInterface::STATE_COMPLETED, $this->order->getPaymentState());
    }

    public function testShouldInitializeItemUnitsCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->order->getItemUnits());
    }

    public function testShouldInitializeShipmentsCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->order->getShipments());
    }

    public function testShouldAddShipmentProperly(): void
    {
        $this->shipment->expects($this->once())->method('setOrder')->with($this->order);
        $this->order->addShipment($this->shipment);

        $this->assertTrue($this->order->hasShipment($this->shipment));
    }

    public function testShouldRemoveShipmentProperly(): void
    {
        $this->order->addShipment($this->shipment);
        $this->shipment->expects($this->once())->method('setOrder')->with(null);

        $this->order->removeShipment($this->shipment);

        $this->assertFalse($this->order->hasShipment($this->shipment));
    }

    public function testShouldRemoveShipmentsWithUnits(): void
    {
        $this->order->addShipment($this->shipment);
        $this->shipment->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection([
            $this->shipmentUnit,
        ]));
        $this->shipment->expects($this->once())->method('removeUnit')->with($this->shipmentUnit);

        $this->order->removeShipments();

        $this->assertFalse($this->order->hasShipment($this->shipment));
    }

    public function testShouldRemoveShipmentWithoutUnits(): void
    {
        $this->order->addShipment($this->shipment);
        $this->shipment->expects($this->once())->method('getUnits')->willReturn(new ArrayCollection());

        $this->order->removeShipments();

        $this->assertFalse($this->order->hasShipment($this->shipment));
    }

    public function testShouldReturnShippingAdjustments(): void
    {
        $this->shippingAdjustment->expects($this->exactly(2))->method('getType')->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $this->shippingAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->shippingAdjustment->expects($this->exactly(3))->method('isNeutral')->willReturn(true);
        $this->taxAdjustment->expects($this->exactly(2))->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->taxAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->taxAdjustment->expects($this->exactly(2))->method('isNeutral')->willReturn(true);

        $this->order->addAdjustment($this->shippingAdjustment);
        $this->order->addAdjustment($this->taxAdjustment);

        $this->assertSame(2, $this->order->getAdjustments()->count());
        $this->assertSame(1, $this->order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->count());
        $this->assertSame(
            $this->shippingAdjustment,
            $this->order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->first(),
        );
    }

    public function testShouldRemoveShippingAdjustments(): void
    {
        $this->shippingAdjustment->method('getType')->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $this->shippingAdjustment->method('isNeutral')->willReturn(true);
        $setAdjustableInvokeCount = $this->exactly(2);
        $this->shippingAdjustment
            ->expects($setAdjustableInvokeCount)
            ->method('setAdjustable')
            ->willReturnCallback(function ($adjustable) use ($setAdjustableInvokeCount) {
                if ($setAdjustableInvokeCount->numberOfInvocations() === 1) {
                    $this->assertSame($this->order, $adjustable);
                }
                if ($setAdjustableInvokeCount->numberOfInvocations() === 2) {
                    $this->assertNull($adjustable);
                }
            });
        $this->taxAdjustment->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->taxAdjustment->method('isNeutral')->willReturn(true);
        $this->taxAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->order->addAdjustment($this->shippingAdjustment);
        $this->order->addAdjustment($this->taxAdjustment);
        $this->order->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        $this->assertSame(
            0,
            $this->order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->count(),
        );
        $this->assertSame(1, $this->order->getAdjustments()->count());
    }

    public function testShouldReturnTaxAdjustments(): void
    {
        $this->shippingAdjustment->method('getType')->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $this->shippingAdjustment->method('isNeutral')->willReturn(true);
        $this->shippingAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->taxAdjustment->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->taxAdjustment->method('isNeutral')->willReturn(true);
        $this->taxAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->order->addAdjustment($this->shippingAdjustment);
        $this->order->addAdjustment($this->taxAdjustment);

        $this->assertSame(2, $this->order->getAdjustments()->count());
        $this->assertSame(1, $this->order->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->count());
        $this->assertSame(
            $this->taxAdjustment,
            $this->order->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->first(),
        );
    }

    public function testShouldRemoveTaxAdjustments(): void
    {
        $this->taxAdjustment->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->taxAdjustment->method('isNeutral')->willReturn(true);
        $setAdjustableInvokeCount = $this->exactly(2);
        $this->taxAdjustment
            ->expects($setAdjustableInvokeCount)
            ->method('setAdjustable')
            ->willReturnCallback(function ($adjustable) use ($setAdjustableInvokeCount) {
                if ($setAdjustableInvokeCount->numberOfInvocations() === 1) {
                    $this->assertSame($this->order, $adjustable);
                }
                if ($setAdjustableInvokeCount->numberOfInvocations() === 2) {
                    $this->assertNull($adjustable);
                }
            });
        $this->shippingAdjustment->method('getType')->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $this->shippingAdjustment->method('isNeutral')->willReturn(true);
        $this->shippingAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);

        $this->order->addAdjustment($this->shippingAdjustment);
        $this->order->addAdjustment($this->taxAdjustment);
        $this->order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

        $this->assertSame(
            0,
            $this->order->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)->count(),
        );
        $this->assertSame(1, $this->order->getAdjustments()->count());
    }

    public function testShouldNotHaveCurrencyCodeDefinedByDefault(): void
    {
        $this->assertNull($this->order->getCurrencyCode());
    }

    public function testShouldAllowDefiningCurrencyCode(): void
    {
        $this->order->setCurrencyCode('PLN');

        $this->assertSame('PLN', $this->order->getCurrencyCode());
    }

    public function testShouldHaveNoDefaultLocaleCode(): void
    {
        $this->assertNull($this->order->getLocaleCode());
    }

    public function testShouldLocaleCodeBeMutable(): void
    {
        $this->order->setLocaleCode('pl');

        $this->assertSame('pl', $this->order->getLocaleCode());
    }

    public function testShouldHaveCartShippingStateByDefault(): void
    {
        $this->assertSame(OrderShippingStates::STATE_CART, $this->order->getShippingState());
    }

    public function testShouldShippingStateBeMutable(): void
    {
        $this->order->setShippingState(OrderShippingStates::STATE_SHIPPED);

        $this->assertSame(OrderShippingStates::STATE_SHIPPED, $this->order->getShippingState());
    }

    public function testShouldAddPayment(): void
    {
        $this->firstPayment->expects($this->once())->method('setOrder')->with($this->order);

        $this->order->addPayment($this->firstPayment);

        $this->assertTrue($this->order->hasPayment($this->firstPayment));
    }

    public function testShouldRemovePayment(): void
    {
        $this->order->addPayment($this->firstPayment);

        $this->order->removePayment($this->firstPayment);

        $this->assertFalse($this->order->hasPayment($this->firstPayment));
    }

    public function testShouldReturnLastPaymentWithGivenState(): void
    {
        $this->firstPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_CART);
        $this->firstPayment->expects($this->once())->method('setOrder')->with($this->order);
        $this->secondPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_CANCELLED);
        $this->secondPayment->expects($this->once())->method('setOrder')->with($this->order);
        $this->thirdPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_PROCESSING);
        $this->thirdPayment->expects($this->once())->method('setOrder')->with($this->order);
        $this->fourthPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_FAILED);
        $this->fourthPayment->expects($this->once())->method('setOrder')->with($this->order);

        $this->order->addPayment($this->firstPayment);
        $this->order->addPayment($this->secondPayment);
        $this->order->addPayment($this->thirdPayment);
        $this->order->addPayment($this->fourthPayment);

        $this->assertSame(
            $this->firstPayment,
            $this->order->getLastPayment(PaymentInterface::STATE_CART),
        );
    }

    public function testShouldReturnNullIfThereIsNoPaymentAfterTryingToGetLastPayment(): void
    {
        $this->assertNull($this->order->getLastPayment(PaymentInterface::STATE_CART));
    }

    public function testShouldReturnLastPaymentWithAnyStateIfThereIsNoTargetStateSpecified(): void
    {
        $this->firstPayment->expects($this->once())->method('setOrder')->with($this->order);
        $this->secondPayment->expects($this->once())->method('setOrder')->with($this->order);
        $this->thirdPayment->expects($this->once())->method('setOrder')->with($this->order);
        $this->fourthPayment->expects($this->once())->method('setOrder')->with($this->order);
        $this->order->addPayment($this->firstPayment);
        $this->order->addPayment($this->secondPayment);
        $this->order->addPayment($this->thirdPayment);
        $this->order->addPayment($this->fourthPayment);

        $this->assertSame(
            $this->fourthPayment,
            $this->order->getLastPayment(),
        );
    }

    public function testShouldAddShipment(): void
    {
        $this->shipment->expects($this->once())->method('setOrder')->with($this->order);

        $this->order->addShipment($this->shipment);

        $this->assertTrue($this->order->hasShipment($this->shipment));
    }

    public function testShouldRemoveShipment(): void
    {
        $this->order->addShipment($this->shipment);
        $this->shipment->expects($this->once())->method('setOrder')->with(null);

        $this->order->removeShipment($this->shipment);

        $this->assertFalse($this->order->hasShipment($this->shipment));
    }

    public function testShouldHavePromotionCoupon(): void
    {
        $coupon = $this->createMock(PromotionCouponInterface::class);

        $this->order->setPromotionCoupon($coupon);

        $this->assertSame($coupon, $this->order->getPromotionCoupon());
    }

    public function testShouldCountPromotionSubjects(): void
    {
        $this->firstItem->expects($this->once())->method('getQuantity')->willReturn(4);
        $this->firstItem->expects($this->once())->method('getTotal')->willReturn(420);
        $this->firstItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->secondItem->expects($this->once())->method('getQuantity')->willReturn(3);
        $this->secondItem->expects($this->once())->method('getTotal')->willReturn(666);
        $this->secondItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->order->addItem($this->firstItem);
        $this->order->addItem($this->secondItem);

        $this->assertSame(7, $this->order->getPromotionSubjectCount());
    }

    public function testShouldCountItemsSubtotal(): void
    {
        $this->firstItem->expects($this->once())->method('getSubtotal')->willReturn(420);
        $this->firstItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->secondItem->expects($this->once())->method('getSubtotal')->willReturn(666);
        $this->secondItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->order->addItem($this->firstItem);
        $this->order->addItem($this->secondItem);

        $this->assertSame(1086, $this->order->getItemsSubtotal());
    }

    public function testShouldAddPromotion(): void
    {
        $this->order->addPromotion($this->promotion);

        $this->assertTrue($this->order->hasPromotion($this->promotion));
    }

    public function testShouldRemovePromotion(): void
    {
        $this->order->addPromotion($this->promotion);

        $this->order->removePromotion($this->promotion);

        $this->assertFalse($this->order->hasPromotion($this->promotion));
    }

    public function testShouldReturnZeroTaxTotalWhenThereAreNoItemsAndAdjustments(): void
    {
        $this->assertSame(0, $this->order->getTaxTotal());
    }

    public function testShouldReturnTaxOfAllItemsAsTaxTotalWenThereAreNoTaxAdjustments(): void
    {
        $this->firstItem->expects($this->once())->method('getTotal')->willReturn(1100);
        $this->firstItem->expects($this->once())->method('getTaxTotal')->willReturn(100);
        $this->firstItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->secondItem->expects($this->once())->method('getTotal')->willReturn(1050);
        $this->secondItem->expects($this->once())->method('getTaxTotal')->willReturn(50);
        $this->secondItem->expects($this->once())->method('setOrder')->with($this->order);

        $this->order->addItem($this->firstItem);
        $this->order->addItem($this->secondItem);

        $this->assertSame(150, $this->order->getTaxTotal());
    }

    public function testShouldReturnTaxOfAllItemsAndNonNeutralShippingTaxAsTaxTotal(): void
    {
        $this->firstItem->expects($this->once())->method('getTotal')->willReturn(1100);
        $this->firstItem->expects($this->once())->method('getTaxTotal')->willReturn(100);
        $this->firstItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->secondItem->expects($this->once())->method('getTotal')->willReturn(1050);
        $this->secondItem->expects($this->once())->method('getTaxTotal')->willReturn(50);
        $this->secondItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->shippingAdjustment->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $this->shippingAdjustment->expects($this->exactly(3))->method('isNeutral')->willReturn(false);
        $this->shippingAdjustment->expects($this->exactly(3))->method('getAmount')->willReturn(1000);
        $this->shippingTaxAdjustment->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->shippingTaxAdjustment->expects($this->exactly(2))->method('isNeutral')->willReturn(false);
        $this->shippingTaxAdjustment->expects($this->exactly(3))->method('getAmount')->willReturn(70);
        $this->order->addItem($this->firstItem);
        $this->order->addItem($this->secondItem);
        $this->shippingAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->shippingTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->order->addAdjustment($this->shippingAdjustment);
        $this->order->addAdjustment($this->shippingTaxAdjustment);

        $this->assertSame(220, $this->order->getTaxTotal());
    }

    public function testShouldReturnTaxOfAllItemsAndNeutralShippingTaxAsTaxTotal(): void
    {
        $this->firstItem->expects($this->once())->method('getTotal')->willReturn(1100);
        $this->firstItem->expects($this->once())->method('getTaxTotal')->willReturn(100);
        $this->firstItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->secondItem->expects($this->once())->method('getTotal')->willReturn(1050);
        $this->secondItem->expects($this->once())->method('getTaxTotal')->willReturn(50);
        $this->secondItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->shippingAdjustment->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $this->shippingAdjustment->expects($this->exactly(3))->method('isNeutral')->willReturn(false);
        $this->shippingAdjustment->expects($this->exactly(3))->method('getAmount')->willReturn(1000);
        $this->shippingTaxAdjustment->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->shippingTaxAdjustment->expects($this->exactly(2))->method('isNeutral')->willReturn(true);
        $this->shippingTaxAdjustment->expects($this->once())->method('getAmount')->willReturn(70);
        $this->order->addItem($this->firstItem);
        $this->order->addItem($this->secondItem);
        $this->shippingAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->shippingTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->order->addAdjustment($this->shippingAdjustment);
        $this->order->addAdjustment($this->shippingTaxAdjustment);

        $this->assertSame(220, $this->order->getTaxTotal());
    }

    public function testShouldIncludeNonNeutralTaxAdjustmentsInShippingTotal(): void
    {
        $this->shippingAdjustment->expects($this->exactly(3))->method('getType')->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $this->shippingAdjustment->expects($this->exactly(4))->method('isNeutral')->willReturn(false);
        $this->shippingAdjustment->expects($this->exactly(4))->method('getAmount')->willReturn(1000);
        $this->shippingTaxAdjustment->expects($this->exactly(3))->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->shippingTaxAdjustment->expects($this->exactly(3))->method('isNeutral')->willReturn(false);
        $this->shippingTaxAdjustment->expects($this->exactly(3))->method('getAmount')->willReturn(70);
        $this->shippingAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->shippingTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->order->addAdjustment($this->shippingAdjustment);
        $this->order->addAdjustment($this->shippingTaxAdjustment);

        $this->assertSame(1070, $this->order->getShippingTotal());
    }

    public function testShouldReturnShippingTotalDecreasedByShippingPromotion(): void
    {
        $shippingPromotionAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->shippingAdjustment->expects($this->exactly(3))->method('getType')->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $this->shippingAdjustment->expects($this->exactly(5))->method('isNeutral')->willReturn(false);
        $this->shippingAdjustment->expects($this->exactly(5))->method('getAmount')->willReturn(1000);
        $this->shippingTaxAdjustment->expects($this->exactly(3))->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->shippingTaxAdjustment->expects($this->exactly(4))->method('isNeutral')->willReturn(false);
        $this->shippingTaxAdjustment->expects($this->exactly(4))->method('getAmount')->willReturn(70);
        $shippingPromotionAdjustment->expects($this->exactly(3))->method('getType')->willReturn(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $shippingPromotionAdjustment->expects($this->exactly(3))->method('isNeutral')->willReturn(false);
        $shippingPromotionAdjustment->expects($this->exactly(3))->method('getAmount')->willReturn(-100);
        $this->shippingAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->shippingTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $shippingPromotionAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->order->addAdjustment($this->shippingAdjustment);
        $this->order->addAdjustment($this->shippingTaxAdjustment);
        $this->order->addAdjustment($shippingPromotionAdjustment);

        $this->assertSame(970, $this->order->getShippingTotal());
    }

    public function testShouldNotIncludeNeutralTaxAdjustmentsInShippingTotal(): void
    {
        $neutralShippingTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $this->shippingAdjustment->expects($this->exactly(3))->method('getType')->willReturn(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $this->shippingAdjustment->expects($this->exactly(4))->method('isNeutral')->willReturn(false);
        $this->shippingAdjustment->expects($this->exactly(4))->method('getAmount')->willReturn(1000);
        $neutralShippingTaxAdjustment->expects($this->exactly(3))->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $neutralShippingTaxAdjustment->expects($this->exactly(3))->method('isNeutral')->willReturn(true);
        $this->shippingAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $neutralShippingTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->order->addAdjustment($this->shippingAdjustment);
        $this->order->addAdjustment($neutralShippingTaxAdjustment);

        $this->assertSame(1000, $this->order->getShippingTotal());
    }

    public function testShouldReturnZeroAsPromotionTotalWhenThereAreNoOrderPromotionAdjustments(): void
    {
        $this->assertSame(0, $this->order->getOrderPromotionTotal());
    }

    public function it_returns_a_sum_of_all_order_promotion_adjustments_order_item_promotion_adjustments_and_order_unit_promotion_adjustments_applied_to_items_as_order_promotion_total(): void
    {
        $orderAdjustment1 = $this->createMock(AdjustmentInterface::class);
        $orderAdjustment2 = $this->createMock(AdjustmentInterface::class);
        $orderItemAdjustment1 = $this->createMock(AdjustmentInterface::class);
        $orderItemAdjustment2 = $this->createMock(AdjustmentInterface::class);
        $orderUnitAdjustment1 = $this->createMock(AdjustmentInterface::class);
        $orderUnitAdjustment2 = $this->createMock(AdjustmentInterface::class);
        $orderAdjustment1->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $orderAdjustment1->expects($this->once())->method('getAmount')->willReturn(-400);
        $orderAdjustment1->expects($this->once())->method('isNeutral')->willReturn(false);
        $orderAdjustment2->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $orderAdjustment2->expects($this->once())->method('getAmount')->willReturn(-600);
        $orderAdjustment2->expects($this->once())->method('isNeutral')->willReturn(false);
        $orderItemAdjustment1->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $orderItemAdjustment1->expects($this->once())->method('getAmount')->willReturn(-100);
        $orderItemAdjustment1->expects($this->once())->method('isNeutral')->willReturn(false);
        $orderItemAdjustment2->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $orderItemAdjustment2->expects($this->once())->method('getAmount')->willReturn(-200);
        $orderItemAdjustment2->expects($this->once())->method('isNeutral')->willReturn(false);
        $orderUnitAdjustment1->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $orderUnitAdjustment1->expects($this->once())->method('getAmount')->willReturn(-50);
        $orderUnitAdjustment1->expects($this->once())->method('isNeutral')->willReturn(false);
        $orderUnitAdjustment1->expects($this->once())->method('getType')->willReturn(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $orderUnitAdjustment1->expects($this->once())->method('getAmount')->willReturn(-20);
        $orderUnitAdjustment1->expects($this->once())->method('isNeutral')->willReturn(false);
        $this->firstItem->expects($this->once())->method('getTotal')->willReturn(500);
        $this->secondItem->expects($this->once())->method('getTotal')->willReturn(300);
        $this->firstItem
            ->expects($this->exactly(3))
            ->method('getAdjustmentsRecursively')
            ->willReturnMap([
                [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, new ArrayCollection([$orderAdjustment1])],
                [AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT, new ArrayCollection([$orderItemAdjustment1])],
                [AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT, new ArrayCollection([$orderUnitAdjustment1])],
            ]);
        $this->secondItem
            ->expects($this->exactly(3))
            ->method('getAdjustmentsRecursively')
            ->willReturnMap([
                [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, new ArrayCollection([$orderAdjustment2])],
                [AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT, new ArrayCollection([$orderItemAdjustment2])],
                [AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT, new ArrayCollection([$orderUnitAdjustment2])],
            ]);
        $this->firstItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->secondItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->order->addItem($this->firstItem);
        $this->order->addItem($this->secondItem);

        $this->assertSame(-1370, $this->order->getOrderPromotionTotal());
    }

    public function testShouldNotIncludeShippingPromotionAdjustmentInOrderPromotionTotal(): void
    {
        $shippingPromotionAdjustment = $this->createMock(AdjustmentInterface::class);
        $orderAdjustment = $this->createMock(AdjustmentInterface::class);
        $orderItemAdjustment = $this->createMock(AdjustmentInterface::class);
        $orderUnitAdjustment = $this->createMock(AdjustmentInterface::class);
        $orderAdjustment->expects($this->once())->method('getAmount')->willReturn(-400);
        $orderAdjustment->expects($this->once())->method('isNeutral')->willReturn(false);
        $orderItemAdjustment->expects($this->once())->method('getAmount')->willReturn(-100);
        $orderItemAdjustment->expects($this->once())->method('isNeutral')->willReturn(false);
        $orderUnitAdjustment->expects($this->once())->method('getAmount')->willReturn(-50);
        $orderUnitAdjustment->expects($this->once())->method('isNeutral')->willReturn(false);
        $this->firstItem->expects($this->once())->method('getTotal')->willReturn(500);
        $this->firstItem
            ->expects($this->exactly(3))
            ->method('getAdjustmentsRecursively')
            ->willReturnMap([
                [AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, new ArrayCollection([$orderAdjustment])],
                [AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT, new ArrayCollection([$orderItemAdjustment])],
                [AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT, new ArrayCollection([$orderUnitAdjustment])],
            ]);

        $shippingPromotionAdjustment->expects($this->exactly(3))->method('getType')->willReturn(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $shippingPromotionAdjustment->expects($this->exactly(2))->method('getAmount')->willReturn(-100);
        $shippingPromotionAdjustment->expects($this->exactly(2))->method('isNeutral')->willReturn(false);
        $this->firstItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->order->addItem($this->firstItem);
        $shippingPromotionAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->order->addAdjustment($shippingPromotionAdjustment);

        $this->assertSame(-550, $this->order->getOrderPromotionTotal());
    }

    public function testShouldIncludeOrderPromotionAdjustmentsOrderItemPromotionAdjustmentsAndOrderUnitPromotionAdjustmentsInOrderPromotionTotal(): void
    {
        $orderAdjustmentForOrder = $this->createMock(AdjustmentInterface::class);
        $orderAdjustmentForOrder->expects($this->exactly(3))->method('getType')->willReturn(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        $orderAdjustmentForOrder->expects($this->exactly(5))->method('getAmount')->willReturn(-120);
        $orderAdjustmentForOrder->expects($this->exactly(5))->method('isNeutral')->willReturn(false);
        $orderAdjustmentForOrder->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->order->addAdjustment($orderAdjustmentForOrder);
        $orderAdjustmentForItem = $this->createMock(AdjustmentInterface::class);
        $orderAdjustmentForItem->expects($this->once())->method('getAmount')->willReturn(-150);
        $orderAdjustmentForItem->expects($this->once())->method('isNeutral')->willReturn(false);
        $orderItemAdjustmentForOrder = $this->createMock(AdjustmentInterface::class);
        $orderItemAdjustmentForOrder->expects($this->exactly(3))->method('getType')->willReturn(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $orderItemAdjustmentForOrder->expects($this->exactly(4))->method('getAmount')->willReturn(-230);
        $orderItemAdjustmentForOrder->expects($this->exactly(4))->method('isNeutral')->willReturn(false);
        $orderItemAdjustmentForOrder->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->order->addAdjustment($orderItemAdjustmentForOrder);
        $orderItemAdjustmentForItem = $this->createMock(AdjustmentInterface::class);
        $orderItemAdjustmentForItem->expects($this->once())->method('getAmount')->willReturn(-250);
        $orderItemAdjustmentForItem->expects($this->once())->method('isNeutral')->willReturn(false);
        $orderUnitAdjustmentForOrder = $this->createMock(AdjustmentInterface::class);
        $orderUnitAdjustmentForOrder->expects($this->exactly(3))->method('getType')->willReturn(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $orderUnitAdjustmentForOrder->expects($this->exactly(3))->method('getAmount')->willReturn(-53);
        $orderUnitAdjustmentForOrder->expects($this->exactly(3))->method('isNeutral')->willReturn(false);
        $orderUnitAdjustmentForOrder->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->order->addAdjustment($orderUnitAdjustmentForOrder);
        $orderUnitAdjustmentForItem = $this->createMock(AdjustmentInterface::class);
        $orderUnitAdjustmentForItem->expects($this->once())->method('getAmount')->willReturn(-20);
        $orderUnitAdjustmentForItem->expects($this->once())->method('isNeutral')->willReturn(false);
        $this->firstItem->expects($this->once())->method('getTotal')->willReturn(200);
        $this->firstItem->expects($this->exactly(3))->method('getAdjustmentsRecursively')->willReturnCallback(fn ($type) => match ($type) {
            AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT => new ArrayCollection([$orderAdjustmentForItem]),
            AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT => new ArrayCollection([$orderItemAdjustmentForItem]),
            AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT => new ArrayCollection([$orderUnitAdjustmentForItem]),
            default => new ArrayCollection(),
        });
        $this->firstItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->order->addItem($this->firstItem);

        $this->assertSame(-823, $this->order->getOrderPromotionTotal());
    }

    public function testShouldHaveTokenValue(): void
    {
        $this->order->setTokenValue('xyzasdxqwe');

        $this->assertSame('xyzasdxqwe', $this->order->getTokenValue());
    }

    public function testShouldHaveCustomerIp(): void
    {
        $this->order->setCustomerIp('127.0.0.1');

        $this->assertSame('127.0.0.1', $this->order->getCustomerIp());
    }

    public function testShouldCalculateTotalOfNonDiscountedItems(): void
    {
        $firstVariant = $this->createMock(ProductVariantInterface::class);
        $secondVariant = $this->createMock(ProductVariantInterface::class);
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $this->firstItem->expects($this->exactly(2))->method('getTotal')->willReturn(500);
        $this->firstItem->expects($this->once())->method('getVariant')->willReturn($firstVariant);
        $this->secondItem->expects($this->once())->method('getTotal')->willReturn(800);
        $this->secondItem->expects($this->once())->method('getVariant')->willReturn($secondVariant);
        $firstVariant
            ->expects($this->once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channel)
            ->willReturn(new ArrayCollection());
        $secondVariant
            ->expects($this->once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channel)
            ->willReturn(new ArrayCollection([$catalogPromotion]));
        $this->firstItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->secondItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->order->addItem($this->firstItem);
        $this->order->addItem($this->secondItem);
        $this->order->setChannel($this->channel);

        $this->assertSame(500, $this->order->getNonDiscountedItemsTotal());
    }

    public function testShouldReturnProperTotalOfTaxesIncludedInPriceOrExcludedFromIt(): void
    {
        $includedUnitTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $excludedUnitTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $includedUnitTaxAdjustment->expects($this->exactly(2))->method('isNeutral')->willReturn(true);
        $includedUnitTaxAdjustment->expects($this->once())->method('getAmount')->willReturn(1000);
        $excludedUnitTaxAdjustment->expects($this->exactly(2))->method('isNeutral')->willReturn(false);
        $excludedUnitTaxAdjustment->expects($this->once())->method('getAmount')->willReturn(800);
        $this->firstItem->expects($this->once())->method('getTotal')->willReturn(5000);
        $this->firstItem
            ->expects($this->exactly(2))
            ->method('getAdjustmentsRecursively')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$includedUnitTaxAdjustment]));
        $this->secondItem->expects($this->once())->method('getTotal')->willReturn(5000);
        $this->secondItem
            ->expects($this->exactly(2))
            ->method('getAdjustmentsRecursively')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$excludedUnitTaxAdjustment]));
        $this->firstItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->secondItem->expects($this->once())->method('setOrder')->with($this->order);
        $this->order->addItem($this->firstItem);
        $this->order->addItem($this->secondItem);
        $this->shippingTaxAdjustment->expects($this->exactly(2))->method('getType')->willReturn(AdjustmentInterface::TAX_ADJUSTMENT);
        $this->shippingTaxAdjustment->expects($this->once())->method('setAdjustable')->with($this->order);
        $this->shippingTaxAdjustment->expects($this->exactly(4))->method('isNeutral')->willReturn(true);
        $this->shippingTaxAdjustment->expects($this->once())->method('getAmount')->willReturn(500);
        $this->order->addAdjustment($this->shippingTaxAdjustment);

        $this->assertSame(1500, $this->order->getTaxIncludedTotal());
        $this->assertSame(800, $this->order->getTaxExcludedTotal());
    }

    public function testShouldBeAbleToProcess(): void
    {
        $this->order->setState(OrderInterface::STATE_CART);

        $this->assertTrue($this->order->canBeProcessed());
    }

    public function testShouldNotBeAbleToProcessIfStateIsNew(): void
    {
        $this->order->setState(OrderInterface::STATE_NEW);

        $this->assertFalse($this->order->canBeProcessed());
    }
}
