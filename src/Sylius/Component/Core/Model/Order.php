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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Sylius\Component\Order\Model\Order as BaseOrder;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface as BaseCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface as BasePromotionInterface;
use Sylius\Component\User\Model\UserInterface as BaseUserInterface;
use Webmozart\Assert\Assert;

class Order extends BaseOrder implements OrderInterface
{
    /**
     * @var CustomerInterface
     */
    protected $customer;

    /**
     * @var ChannelInterface
     */
    protected $channel;

    /**
     * @var AddressInterface
     */
    protected $shippingAddress;

    /**
     * @var AddressInterface
     */
    protected $billingAddress;

    /**
     * @var Collection|BasePaymentInterface[]
     */
    protected $payments;

    /**
     * @var Collection|ShipmentInterface[]
     */
    protected $shipments;

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @var string
     */
    protected $localeCode;

    /**
     * @var BaseCouponInterface
     */
    protected $promotionCoupon;

    /**
     * @var string
     */
    protected $checkoutState = OrderCheckoutStates::STATE_CART;

    /**
     * @var string
     */
    protected $paymentState = OrderPaymentStates::STATE_CART;

    /**
     * @var string
     */
    protected $shippingState = OrderShippingStates::STATE_CART;

    /**
     * @var Collection|BasePromotionInterface[]
     */
    protected $promotions;

    /**
     * @var string
     */
    protected $tokenValue;

    /**
     * @var string
     */
    protected $customerIp;

    public function __construct()
    {
        parent::__construct();

        $this->payments = new ArrayCollection();
        $this->shipments = new ArrayCollection();
        $this->promotions = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer(): ?BaseCustomerInterface
    {
        return $this->customer;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomer(?BaseCustomerInterface $customer): void
    {
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $this->customer = $customer;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel(): ?BaseChannelInterface
    {
        return $this->channel;
    }

    /**
     * {@inheritdoc}
     */
    public function setChannel(?BaseChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): ?BaseUserInterface
    {
        if (null === $this->customer) {
            return null;
        }

        return $this->customer->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress(): ?AddressInterface
    {
        return $this->shippingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress(?AddressInterface $address): void
    {
        $this->shippingAddress = $address;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress(): ?AddressInterface
    {
        return $this->billingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress(?AddressInterface $address): void
    {
        $this->billingAddress = $address;
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckoutState(): ?string
    {
        return $this->checkoutState;
    }

    /**
     * {@inheritdoc}
     */
    public function setCheckoutState(?string $checkoutState): void
    {
        $this->checkoutState = $checkoutState;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentState(): ?string
    {
        return $this->paymentState;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentState(?string $paymentState): void
    {
        $this->paymentState = $paymentState;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemUnits(): Collection
    {
        $units = new ArrayCollection();

        /** @var OrderItem $item */
        foreach ($this->getItems() as $item) {
            foreach ($item->getUnits() as $unit) {
                $units->add($unit);
            }
        }

        return $units;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemUnitsByVariant(ProductVariantInterface $variant): Collection
    {
        return $this->getItemUnits()->filter(function (OrderItemUnitInterface $itemUnit) use ($variant): bool {
            return $variant === $itemUnit->getStockable();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPayments(): bool
    {
        return !$this->payments->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addPayment(BasePaymentInterface $payment): void
    {
        /** @var PaymentInterface $payment */
        Assert::isInstanceOf($payment, PaymentInterface::class);

        if (!$this->hasPayment($payment)) {
            $this->payments->add($payment);
            $payment->setOrder($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removePayment(BasePaymentInterface $payment): void
    {
        /** @var PaymentInterface $payment */
        Assert::isInstanceOf($payment, PaymentInterface::class);

        if ($this->hasPayment($payment)) {
            $this->payments->removeElement($payment);
            $payment->setOrder(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasPayment(BasePaymentInterface $payment): bool
    {
        return $this->payments->contains($payment);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * @return bool
     */
    public function isShippingRequired(): bool
    {
        foreach ($this->getItems() as $orderItem) {
            if ($orderItem->getVariant()->isShippingRequired()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getShipments(): Collection
    {
        return $this->shipments;
    }

    /**
     * {@inheritdoc}
     */
    public function hasShipments(): bool
    {
        return !$this->shipments->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addShipment(ShipmentInterface $shipment): void
    {
        if (!$this->hasShipment($shipment)) {
            $shipment->setOrder($this);
            $this->shipments->add($shipment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeShipment(ShipmentInterface $shipment): void
    {
        if ($this->hasShipment($shipment)) {
            $shipment->setOrder(null);
            $this->shipments->removeElement($shipment);
        }
    }

    public function removeShipments(): void
    {
        $this->shipments->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function hasShipment(ShipmentInterface $shipment): bool
    {
        return $this->shipments->contains($shipment);
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionCoupon(): ?BaseCouponInterface
    {
        return $this->promotionCoupon;
    }

    /**
     * {@inheritdoc}
     */
    public function setPromotionCoupon(?BaseCouponInterface $coupon): void
    {
        $this->promotionCoupon = $coupon;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionSubjectTotal(): int
    {
        return $this->getItemsTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionSubjectCount(): int
    {
        return $this->getTotalQuantity();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrencyCode(?string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocaleCode(?string $localeCode): void
    {
        Assert::string($localeCode);

        $this->localeCode = $localeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingState(): ?string
    {
        return $this->shippingState;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingState(?string $state): void
    {
        $this->shippingState = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPromotion(BasePromotionInterface $promotion): bool
    {
        return $this->promotions->contains($promotion);
    }

    /**
     * {@inheritdoc}
     */
    public function addPromotion(BasePromotionInterface $promotion): void
    {
        if (!$this->hasPromotion($promotion)) {
            $this->promotions->add($promotion);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removePromotion(BasePromotionInterface $promotion): void
    {
        if ($this->hasPromotion($promotion)) {
            $this->promotions->removeElement($promotion);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    /**
     * Returns sum of neutral and non neutral tax adjustments on order and total tax of order items.
     *
     * {@inheritdoc}
     */
    public function getTaxTotal(): int
    {
        $taxTotal = 0;

        foreach ($this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $taxAdjustment) {
            $taxTotal += $taxAdjustment->getAmount();
        }
        foreach ($this->items as $item) {
            $taxTotal += $item->getTaxTotal();
        }

        return $taxTotal;
    }

    /**
     * Returns shipping fee together with taxes decreased by shipping discount.
     *
     * {@inheritdoc}
     */
    public function getShippingTotal(): int
    {
        $shippingTotal = $this->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $shippingTotal += $this->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $shippingTotal += $this->getAdjustmentsTotal(AdjustmentInterface::TAX_ADJUSTMENT);

        return $shippingTotal;
    }

    /**
     * Returns amount of order discount. Does not include order item and shipping discounts.
     *
     * {@inheritdoc}
     */
    public function getOrderPromotionTotal(): int
    {
        $orderPromotionTotal = 0;

        foreach ($this->items as $item) {
            $orderPromotionTotal += $item->getAdjustmentsTotalRecursively(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
        }

        return $orderPromotionTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenValue(): ?string
    {
        return $this->tokenValue;
    }

    /**
     * {@inheritdoc}
     */
    public function setTokenValue(?string $tokenValue): void
    {
        $this->tokenValue = $tokenValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerIp(): ?string
    {
        return $this->customerIp;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerIp(?string $customerIp): void
    {
        $this->customerIp = $customerIp;
    }
}
