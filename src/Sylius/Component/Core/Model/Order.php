<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Cart\Model\Cart;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Promotion\Model\CouponInterface as BaseCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;


/**
 * Order entity.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Order extends Cart implements OrderInterface
{
    /**
     * User.
     *
     * @var UserInterface
     */
    protected $user;

    /**
     * Order shipping address.
     *
     * @var AddressInterface
     */
    protected $shippingAddress;

    /**
     * Order billing address.
     *
     * @var AddressInterface
     */
    protected $billingAddress;

    /**
     * Payments for this order.
     *
     * @var Collection|BasePaymentInterface[]
     */
    protected $payments;

    /**
     * Shipments for this order.
     *
     * @var Collection|ShipmentInterface[]
     */
    protected $shipments;

    /**
     * Currency ISO code.
     *
     * @var string
     */
    protected $currency;

    /**
     * Promotion coupons.
     *
     * @var Collection|BaseCouponInterface[]
     */
    protected $promotionCoupons;

    /**
     * Order payment state.
     *
     * @var string
     */
    protected $paymentState = BasePaymentInterface::STATE_NEW;

    /**
     * Order shipping state.
     * It depends on the status of all order shipments.
     *
     * @var string
     */
    protected $shippingState = OrderShippingStates::CHECKOUT;

    /**
     * Promotions applied
     *
     * @var Collection|PromotionInterface[]
     */
    protected $promotions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->payments = new ArrayCollection();
        $this->shipments = new ArrayCollection();
        $this->promotionCoupons = new ArrayCollection();
        $this->promotions = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(UserInterface $user = null)
    {
        $this->user = $user;
        if (null !== $this->user) {
            $this->email = $this->user->getEmail();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress(AddressInterface $address)
    {
        $this->shippingAddress = $address;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress(AddressInterface $address)
    {
        $this->billingAddress = $address;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentState()
    {
        return $this->paymentState;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentState($paymentState)
    {
        $this->paymentState = $paymentState;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryUnits()
    {
        $units = new ArrayCollection();

        /** @var $item OrderItem */
        foreach ($this->getItems() as $item) {
            foreach ($item->getInventoryUnits() as $unit) {
                $units->add($unit);
            }
        }

        return $units;
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryUnitsByVariant(ProductVariantInterface $variant)
    {
        return $this->getInventoryUnits()->filter(function (InventoryUnitInterface $unit) use ($variant) {
            return $variant === $unit->getStockable();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPayments()
    {
        return !$this->payments->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addPayment(BasePaymentInterface $payment)
    {
        /** @var $payment PaymentInterface */
        if (!$this->hasPayment($payment)) {
            $this->payments->add($payment);
            $payment->setOrder($this);

            $this->setPaymentState($payment->getState());
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removePayment(BasePaymentInterface $payment)
    {
        /** @var $payment PaymentInterface */
        if ($this->hasPayment($payment)) {
            $this->payments->removeElement($payment);
            $payment->setOrder(null);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPayment(BasePaymentInterface $payment)
    {
        return $this->payments->contains($payment);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastPayment($state = BasePaymentInterface::STATE_NEW)
    {
        if ($this->payments->isEmpty()) {
            return false;
        }

        return $this->payments->filter(function (BasePaymentInterface $payment) use ($state) {
            return $payment->getState() === $state;
        })->last();
    }

    /**
     * {@inheritdoc}
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * {@inheritdoc}
     */
    public function hasShipments()
    {
        return !$this->shipments->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addShipment(ShipmentInterface $shipment)
    {
        if (!$this->hasShipment($shipment)) {
            $shipment->setOrder($this);
            $this->shipments->add($shipment);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeShipment(ShipmentInterface $shipment)
    {
        if ($this->hasShipment($shipment)) {
            $shipment->setOrder(null);
            $this->shipments->removeElement($shipment);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasShipment(ShipmentInterface $shipment)
    {
        return $this->shipments->contains($shipment);
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionCoupons()
    {
        return $this->promotionCoupons;
    }

    /**
     * {@inheritdoc}
     */
    public function addPromotionCoupon($coupon)
    {
        if (null === $coupon) {
            return $this;
        }

        if (!$coupon instanceof BaseCouponInterface) {
            throw new UnexpectedTypeException($coupon, 'Sylius\Component\Promotion\Model\CouponInterface');
        }

        if (!$this->hasPromotionCoupon($coupon)) {
            $this->promotionCoupons->add($coupon);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removePromotionCoupon($coupon)
    {
        if (null === $coupon) {
            return $this;
        }

        if (!$coupon instanceof BaseCouponInterface) {
            throw new UnexpectedTypeException($coupon, 'Sylius\Component\Promotion\Model\CouponInterface');
        }

        if ($this->hasPromotionCoupon($coupon)) {
            $this->promotionCoupons->removeElement($coupon);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPromotionCoupon($coupon)
    {
        return $this->promotionCoupons->contains($coupon);
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionSubjectTotal()
    {
        return $this->getItemsTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionSubjectCount()
    {
        return $this->items->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingState()
    {
        return $this->shippingState;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingState($state)
    {
        $this->shippingState = $state;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isBackorder()
    {
        foreach ($this->getInventoryUnits() as $unit) {
            if (InventoryUnitInterface::STATE_BACKORDERED === $unit->getInventoryState()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the last updated shipment of the order
     *
     * @return false|ShipmentInterface
     */
    public function getLastShipment()
    {
        if ($this->shipments->isEmpty()) {
            return false;
        }

        $last = $this->shipments->first();
        foreach ($this->shipments as $shipment) {
            if ($shipment->getUpdatedAt() > $last->getUpdatedAt()) {
                $last = $shipment;
            }
        }

        return $last;
    }

    /**
     * {@inheritdoc}
     */
    public function isInvoiceAvailable()
    {
        if (null !== $lastShipment = $this->getLastShipment()) {
            return in_array($lastShipment->getState(), array(ShipmentInterface::STATE_RETURNED, ShipmentInterface::STATE_SHIPPED));
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPromotion(PromotionInterface $promotion)
    {
        return $this->promotions->contains($promotion);
    }

    /**
     * {@inheritdoc}
     */
    public function addPromotion(PromotionInterface $promotion)
    {
        if (!$this->hasPromotion($promotion)) {
            $this->promotions->add($promotion);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removePromotion(PromotionInterface $promotion)
    {
        if ($this->hasPromotion($promotion)) {
            $this->promotions->removeElement($promotion);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotions()
    {
        return $this->promotions;
    }
}
