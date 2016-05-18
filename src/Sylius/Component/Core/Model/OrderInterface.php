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
use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Payment\Model\PaymentsSubjectInterface;
use Sylius\Component\Promotion\Model\CouponInterface as BaseCouponInterface;
use Sylius\Component\Promotion\Model\PromotionCountableSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponAwareSubjectInterface;
use Sylius\Component\User\Model\CustomerAwareInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderInterface extends
    CartInterface,
    PaymentsSubjectInterface,
    PromotionCountableSubjectInterface,
    PromotionCouponAwareSubjectInterface,
    CustomerAwareInterface,
    ChannelAwareInterface
{
    /**
     * @return null|UserInterface
     */
    public function getUser();

    /**
     * @return AddressInterface
     */
    public function getShippingAddress();

    /**
     * @param AddressInterface $address
     */
    public function setShippingAddress(AddressInterface $address);

    /**
     * @return AddressInterface
     */
    public function getBillingAddress();

    /**
     * @param AddressInterface $address
     */
    public function setBillingAddress(AddressInterface $address);

    /**
     * @return string
     */
    public function getCheckoutState();

    /**
     * @param string $checkoutState
     */
    public function setCheckoutState($checkoutState);

    /**
     * @return string
     */
    public function getPaymentState();

    /**
     * @param string $paymentState
     */
    public function setPaymentState($paymentState);

    /**
     * @return Collection|OrderItemUnitInterface[]
     */
    public function getItemUnits();

    /**
     * @param ProductVariantInterface $variant
     *
     * @return Collection|OrderItemUnitInterface[]
     */
    public function getItemUnitsByVariant(ProductVariantInterface $variant);

    /**
     * @return Collection|ShipmentInterface[]
     */
    public function getShipments();

    /**
     * @return bool
     */
    public function hasShipments();

    /**
     * @param ShipmentInterface $shipment
     */
    public function addShipment(ShipmentInterface $shipment);

    /**
     * @param ShipmentInterface $shipment
     */
    public function removeShipment(ShipmentInterface $shipment);

    /**
     * @param ShipmentInterface $shipment
     *
     * @return bool
     */
    public function hasShipment(ShipmentInterface $shipment);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string
     *
     * @return OrderInterface
     */
    public function setCurrency($currency);

    /**
     * @return float
     */
    public function getExchangeRate();

    /**
     * @param float $exchangeRate
     */
    public function setExchangeRate($exchangeRate);

    /**
     * @param BaseCouponInterface $coupon
     */
    public function setPromotionCoupon(BaseCouponInterface $coupon = null);

    /**
     * @return string
     */
    public function getShippingState();

    /**
     * @param string $state
     */
    public function setShippingState($state);

    /**
     * @return bool
     */
    public function isBackorder();

    /**
     * @return ShipmentInterface
     */
    public function getLastShipment();

    /**
     * @param $state
     *
     * @return null|PaymentInterface
     */
    public function getLastPayment($state = PaymentInterface::STATE_NEW);

    /**
     * @return bool
     */
    public function isInvoiceAvailable();

    /**
     * @return int
     */
    public function getTaxTotal();

    /**
     * @return int
     */
    public function getShippingTotal();

    /**
     * @return int
     */
    public function getOrderPromotionTotal();
}
