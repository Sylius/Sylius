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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Payment\Model\PaymentsSubjectInterface;
use Sylius\Component\Promotion\Model\CouponInterface as BaseCouponInterface;
use Sylius\Component\Promotion\Model\PromotionCountableSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponsAwareSubjectInterface;

/**
 * Sylius core Order model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderInterface extends CartInterface, PaymentsSubjectInterface, PromotionCountableSubjectInterface, PromotionCouponsAwareSubjectInterface, UserAwareInterface
{
    /**
     * Get shipping address.
     *
     * @return AddressInterface
     */
    public function getShippingAddress();

    /**
     * Set shipping address.
     *
     * @param AddressInterface $address
     */
    public function setShippingAddress(AddressInterface $address);

    /**
     * Get billing address.
     *
     * @return AddressInterface
     */
    public function getBillingAddress();

    /**
     * Set billing address.
     *
     * @param AddressInterface $address
     */
    public function setBillingAddress(AddressInterface $address);

    /**
     * Get the payment state.
     *
     * @return string
     */
    public function getPaymentState();

    /**
     * Set the payment state.
     *
     * @param string $paymentState
     */
    public function setPaymentState($paymentState);

    /**
     * Get all inventory units.
     *
     * @return Collection|InventoryUnitInterface[]
     */
    public function getInventoryUnits();

    /**
     * Get all inventory units by the product variant.
     *
     * @param ProductVariantInterface $variant
     *
     * @return Collection|InventoryUnitInterface[]
     */
    public function getInventoryUnitsByVariant(ProductVariantInterface $variant);

    /**
     * Get all shipments associated with this order.
     *
     * @return Collection|ShipmentInterface[]
     */
    public function getShipments();

    /**
     * Check if order has any shipments.
     *
     * @return bool
     */
    public function hasShipments();

    /**
     * Add a shipment.
     *
     * @param ShipmentInterface $shipment
     */
    public function addShipment(ShipmentInterface $shipment);

    /**
     * Remove shipment.
     *
     * @param ShipmentInterface $shipment
     */
    public function removeShipment(ShipmentInterface $shipment);

    /**
     * Has shipment?
     *
     * @param ShipmentInterface $shipment
     *
     * @return bool
     */
    public function hasShipment(ShipmentInterface $shipment);

    /**
     * Get currency.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set currency.
     *
     * @param string
     *
     * @return OrderInterface
     */
    public function setCurrency($currency);

    /**
     * Adds promotion coupon.
     *
     * @param BaseCouponInterface $coupon
     */
    public function addPromotionCoupon($coupon);

    /**
     * Removes promotion coupon.
     *
     * @param BaseCouponInterface $coupon
     */
    public function removePromotionCoupon($coupon);

    /**
     * Has promotion coupon?
     *
     * @param BaseCouponInterface $coupon
     */
    public function hasPromotionCoupon($coupon);

    /**
     * Get the shipping state.
     *
     * @return string
     */
    public function getShippingState();

    /**
     * Set shipping state.
     *
     * @param string $state
     */
    public function setShippingState($state);

    /**
     * Has any pending inventory?
     *
     * @return bool
     */
    public function isBackorder();

    /**
     * Gets the last updated shipment of the order
     *
     * @return ShipmentInterface
     */
    public function getLastShipment();

    /**
     * Gets the last new payment of the order
     *
     * @param $state
     *
     * @return PaymentInterface
     */
    public function getLastPayment($state = PaymentInterface::STATE_NEW);

    /**
     * Tells is the invoice of the order can be generated.
     *
     * @return bool
     */
    public function isInvoiceAvailable();
}
