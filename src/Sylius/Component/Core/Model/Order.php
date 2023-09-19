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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Sylius\Component\Order\Model\AdjustmentInterface as BaseAdjustmentInterface;
use Sylius\Component\Order\Model\Order as BaseOrder;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface as BaseCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface as BasePromotionInterface;
use Sylius\Component\User\Model\UserInterface as BaseUserInterface;
use Webmozart\Assert\Assert;

class Order extends BaseOrder implements OrderInterface
{
    /** @var CustomerInterface|null */
    protected $customer;

    /** @var ChannelInterface|null */
    protected $channel;

    /** @var AddressInterface|null */
    protected $shippingAddress;

    /** @var AddressInterface|null */
    protected $billingAddress;

    /**
     * @var Collection|PaymentInterface[]
     *
     * @psalm-var Collection<array-key, PaymentInterface>
     */
    protected $payments;

    /**
     * @var Collection|ShipmentInterface[]
     *
     * @psalm-var Collection<array-key, ShipmentInterface>
     */
    protected $shipments;

    /** @var string|null */
    protected $currencyCode;

    /** @var string|null */
    protected $localeCode;

    /** @var BaseCouponInterface|null */
    protected $promotionCoupon;

    /** @var string */
    protected $checkoutState = OrderCheckoutStates::STATE_CART;

    /** @var string */
    protected $paymentState = OrderPaymentStates::STATE_CART;

    /** @var string */
    protected $shippingState = OrderShippingStates::STATE_CART;

    /**
     * @var Collection|BasePromotionInterface[]
     *
     * @psalm-var Collection<array-key, BasePromotionInterface>
     */
    protected $promotions;

    /** @var string|null */
    protected $tokenValue;

    /** @var string|null */
    protected $customerIp;

    protected bool $createdByGuest = true;

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, PaymentInterface> $this->payments */
        $this->payments = new ArrayCollection();

        /** @var ArrayCollection<array-key, ShipmentInterface> $this->shipments */
        $this->shipments = new ArrayCollection();

        /** @var ArrayCollection<array-key, BasePromotionInterface> $this->promotions */
        $this->promotions = new ArrayCollection();
    }

    public function getCustomer(): ?BaseCustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(?BaseCustomerInterface $customer): void
    {
        Assert::nullOrisInstanceOf($customer, CustomerInterface::class);

        $this->customer = $customer;
    }

    public function setCustomerWithAuthorization(?BaseCustomerInterface $customer): void
    {
        $this->setCustomer($customer);
        $this->createdByGuest = false;
    }

    public function getChannel(): ?BaseChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?BaseChannelInterface $channel): void
    {
        Assert::isInstanceOf($channel, ChannelInterface::class);
        $this->channel = $channel;
    }

    public function getUser(): ?BaseUserInterface
    {
        if (null === $this->customer) {
            return null;
        }

        return $this->customer->getUser();
    }

    public function getShippingAddress(): ?AddressInterface
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?AddressInterface $address): void
    {
        $this->shippingAddress = $address;
    }

    public function getBillingAddress(): ?AddressInterface
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?AddressInterface $address): void
    {
        $this->billingAddress = $address;
    }

    public function getCheckoutState(): ?string
    {
        return $this->checkoutState;
    }

    public function setCheckoutState(?string $checkoutState): void
    {
        $this->checkoutState = $checkoutState;
    }

    public function getPaymentState(): ?string
    {
        return $this->paymentState;
    }

    public function setPaymentState(?string $paymentState): void
    {
        $this->paymentState = $paymentState;
    }

    public function getItemUnits(): Collection
    {
        /** @var ArrayCollection<array-key, OrderItemUnitInterface> $units */
        $units = new ArrayCollection();

        /** @var OrderItem $item */
        foreach ($this->getItems() as $item) {
            foreach ($item->getUnits() as $unit) {
                Assert::isInstanceOf($unit, OrderItemUnitInterface::class);

                $units->add($unit);
            }
        }

        return $units;
    }

    public function getItemUnitsByVariant(ProductVariantInterface $variant): Collection
    {
        return $this->getItemUnits()->filter(function (OrderItemUnitInterface $itemUnit) use ($variant): bool {
            return $variant === $itemUnit->getStockable();
        });
    }

    /**
     * @psalm-suppress InvalidReturnType https://github.com/doctrine/collections/pull/220
     * @psalm-suppress InvalidReturnStatement https://github.com/doctrine/collections/pull/220
     */
    public function getPayments(): Collection
    {
        /** @phpstan-ignore-next-line */
        return $this->payments;
    }

    public function hasPayments(): bool
    {
        return !$this->payments->isEmpty();
    }

    public function addPayment(BasePaymentInterface $payment): void
    {
        /** @var PaymentInterface $payment */
        Assert::isInstanceOf($payment, PaymentInterface::class);

        if (!$this->hasPayment($payment)) {
            $this->payments->add($payment);
            $payment->setOrder($this);
        }
    }

    public function removePayment(BasePaymentInterface $payment): void
    {
        /** @var PaymentInterface $payment */
        Assert::isInstanceOf($payment, PaymentInterface::class);

        if ($this->hasPayment($payment)) {
            $this->payments->removeElement($payment);
            $payment->setOrder(null);
        }
    }

    public function hasPayment(BasePaymentInterface $payment): bool
    {
        return $this->payments->contains($payment);
    }

    public function getLastPayment(?string $state = null): ?PaymentInterface
    {
        if ($this->payments->isEmpty()) {
            return null;
        }

        $payment = $this->payments->filter(function (BasePaymentInterface $payment) use ($state): bool {
            return null === $state || $payment->getState() === $state;
        })->last();

        return $payment !== false ? $payment : null;
    }

    public function isShippingRequired(): bool
    {
        foreach ($this->items as $orderItem) {
            /** @var OrderItemInterface $orderItem */
            Assert::isInstanceOf($orderItem, OrderItemInterface::class);

            if ($orderItem->getVariant()->isShippingRequired()) {
                return true;
            }
        }

        return false;
    }

    public function getShipments(): Collection
    {
        return $this->shipments;
    }

    public function hasShipments(): bool
    {
        return !$this->shipments->isEmpty();
    }

    public function addShipment(ShipmentInterface $shipment): void
    {
        if (!$this->hasShipment($shipment)) {
            $shipment->setOrder($this);
            $this->shipments->add($shipment);
        }
    }

    public function removeShipment(ShipmentInterface $shipment): void
    {
        if ($this->hasShipment($shipment)) {
            $shipment->setOrder(null);
            $this->shipments->removeElement($shipment);
        }
    }

    public function removeShipments(): void
    {
        // Disassociate OrderItemUnit from all shipments before removal
        foreach ($this->shipments as $shipment) {
            foreach ($shipment->getUnits() as $unit) {
                $shipment->removeUnit($unit);
            }
        }

        $this->shipments->clear();
    }

    public function hasShipment(ShipmentInterface $shipment): bool
    {
        return $this->shipments->contains($shipment);
    }

    public function getPromotionCoupon(): ?BaseCouponInterface
    {
        return $this->promotionCoupon;
    }

    public function setPromotionCoupon(?BaseCouponInterface $coupon): void
    {
        $this->promotionCoupon = $coupon;
    }

    public function getPromotionSubjectTotal(): int
    {
        return $this->getItemsTotal();
    }

    public function getPromotionSubjectCount(): int
    {
        return $this->getTotalQuantity();
    }

    public function getItemsSubtotal(): int
    {
        /** @var array<OrderItemInterface> $items */
        $items = $this->getItems()->toArray();

        return array_reduce(
            $items,
            static function (int $subtotal, OrderItemInterface $item): int {
                return $subtotal + $item->getSubtotal();
            },
            0,
        );
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(?string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(?string $localeCode): void
    {
        Assert::string($localeCode);

        $this->localeCode = $localeCode;
    }

    public function getShippingState(): ?string
    {
        return $this->shippingState;
    }

    public function setShippingState(?string $state): void
    {
        $this->shippingState = $state;
    }

    public function hasPromotion(BasePromotionInterface $promotion): bool
    {
        return $this->promotions->contains($promotion);
    }

    public function addPromotion(BasePromotionInterface $promotion): void
    {
        if (!$this->hasPromotion($promotion)) {
            $this->promotions->add($promotion);
        }
    }

    public function removePromotion(BasePromotionInterface $promotion): void
    {
        if ($this->hasPromotion($promotion)) {
            $this->promotions->removeElement($promotion);
        }
    }

    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    /**
     * Returns sum of neutral and non neutral tax adjustments on order and total tax of order items.
     */
    public function getTaxTotal(): int
    {
        $taxTotal = 0;

        foreach ($this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $taxAdjustment) {
            $taxTotal += $taxAdjustment->getAmount();
        }
        foreach ($this->items as $item) {
            /** @var OrderItemInterface $item */
            Assert::isInstanceOf($item, OrderItemInterface::class);

            $taxTotal += $item->getTaxTotal();
        }

        return $taxTotal;
    }

    public function getTaxExcludedTotal(): int
    {
        return array_reduce(
            $this->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->toArray(),
            static fn (int $total, BaseAdjustmentInterface $adjustment) => !$adjustment->isNeutral() ? $total + $adjustment->getAmount() : $total,
            0,
        );
    }

    public function getTaxIncludedTotal(): int
    {
        return array_reduce(
            $this->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->toArray(),
            static fn (int $total, BaseAdjustmentInterface $adjustment) => $adjustment->isNeutral() ? $total + $adjustment->getAmount() : $total,
            0,
        );
    }

    /**
     * Returns shipping fee together with taxes decreased by shipping discount.
     */
    public function getShippingTotal(): int
    {
        $shippingTotal = $this->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingTotal += $this->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $shippingTotal += $this->getAdjustmentsTotal(AdjustmentInterface::TAX_ADJUSTMENT);

        return $shippingTotal;
    }

    /**
     * Returns amount of order discount. Does not include shipping discounts.
     */
    public function getOrderPromotionTotal(): int
    {
        return
            $this->getAdjustmentsTotalRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT) +
            $this->getAdjustmentsTotalRecursively(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT) +
            $this->getAdjustmentsTotalRecursively(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT)
        ;
    }

    public function getTokenValue(): ?string
    {
        return $this->tokenValue;
    }

    public function setTokenValue(?string $tokenValue): void
    {
        $this->tokenValue = $tokenValue;
    }

    public function getCustomerIp(): ?string
    {
        return $this->customerIp;
    }

    public function setCustomerIp(?string $customerIp): void
    {
        $this->customerIp = $customerIp;
    }

    public function getNonDiscountedItemsTotal(): int
    {
        $total = 0;
        /** @var OrderItemInterface $item */
        foreach ($this->items as $item) {
            $variant = $item->getVariant();
            if ($variant->getAppliedPromotionsForChannel($this->channel)->isEmpty()) {
                $total += $item->getTotal();
            }
        }

        return $total;
    }

    public function isCreatedByGuest(): bool
    {
        return $this->createdByGuest;
    }

    public function getCreatedByGuest(): bool
    {
        trigger_deprecation(
            'sylius/core',
            '1.12',
            'This method is deprecated and it will be removed in Sylius 2.0. Please use `isCreatedByGuest` instead.',
        );

        return $this->isCreatedByGuest();
    }

    public function setCreatedByGuest(bool $createdByGuest): void
    {
        trigger_deprecation(
            'sylius/core',
            '1.12',
            'This method is deprecated and it will be removed in Sylius 2.0. This flag should be changed only through `setCustomerWithAuthorization` method.',
        );

        $this->createdByGuest = $createdByGuest;
    }
}
