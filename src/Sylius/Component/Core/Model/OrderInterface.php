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
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * Sylius core Order model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderInterface extends CartInterface, PromotionSubjectInterface
{
    // Labels for tax, shipping and promotion adjustments.
    const TAX_ADJUSTMENT       = 'tax';
    const SHIPPING_ADJUSTMENT  = 'shipping';
    const PROMOTION_ADJUSTMENT = 'promotion';

    /**
     * Get user.
     *
     * @return UserInterface
     */
    public function getUser();

    /**
     * Set user.
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

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
     * Get the tax total.
     *
     * @return float
     */
    public function getTaxTotal();

    /**
     * Get all tax adjustments.
     *
     * @return Collection|AdjustmentInterface[]
     */
    public function getTaxAdjustments();

    /**
     * Remove all tax adjustments.
     */
    public function removeTaxAdjustments();

    /**
     * Get the promotion total.
     *
     * @return float
     */
    public function getPromotionTotal();

    /**
     * Get all promotion adjustments.
     *
     * @return Collection|AdjustmentInterface[]
     */
    public function getPromotionAdjustments();

    /**
     * Remove all promotion adjustments.
     */
    public function removePromotionAdjustments();

    /**
     * Get shipping total.
     *
     * @return float
     */
    public function getShippingTotal();

    /**
     * Get all shipping adjustments.
     *
     * @return Collection|AdjustmentInterface[]
     */
    public function getShippingAdjustments();

    /**
     * Remove all shipping adjustments.
     */
    public function removeShippingAdjustments();

    /**
     * Get the payment associated with the order.
     *
     * @return PaymentInterface
     */
    public function getPayment();

    /**
     * Set payment.
     *
     * @param PaymentInterface $payment
     */
    public function setPayment(PaymentInterface $payment);

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
     * @param VariantInterface $variant
     *
     * @return Collection|InventoryUnitInterface[]
     */
    public function getInventoryUnitsByVariant(VariantInterface $variant);

    /**
     * Get all shipments associated with this order.
     *
     * @return Collection|ShipmentInterface[]
     */
    public function getShipments();

    /**
     * Check if order has any shipments.
     *
     * @return Boolean
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
     * @return Boolean
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
     * Set promotion coupon.
     *
     * @param CouponInterface $coupon
     */
    public function setPromotionCoupon(CouponInterface $coupon = null);

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
     * @return Boolean
     */
    public function isBackorder();

    /**
     * Gets the last updated shipment of the order
     *
     * @return ShipmentInterface
     */
    public function getLastShipment();
}
