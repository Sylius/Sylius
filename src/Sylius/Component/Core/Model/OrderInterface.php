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
use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Payment\Model\PaymentsSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface as BaseCouponInterface;
use Sylius\Component\Promotion\Model\CountablePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponAwarePromotionSubjectInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderInterface extends
    BaseOrderInterface,
    PaymentsSubjectInterface,
    CountablePromotionSubjectInterface,
    PromotionCouponAwarePromotionSubjectInterface,
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

    public function removeShipments();

    /**
     * @param ShipmentInterface $shipment
     *
     * @return bool
     */
    public function hasShipment(ShipmentInterface $shipment);

    /**
     * @return string
     */
    public function getCurrencyCode();

    /**
     * @param string
     *
     * @throws \InvalidArgumentException
     */
    public function setCurrencyCode($currencyCode);

    /**
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getLocaleCode();

    /**
     * @param string
     */
    public function setLocaleCode($localeCode);

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
     * @return PaymentInterface|null
     */
    public function getLastCartPayment();

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

    /**
     * @return string
     */
    public function getTokenValue();

    /**
     * @param string $tokenValue
     */
    public function setTokenValue($tokenValue);

    /**
     * @return string
     */
    public function getCustomerIp();

    /**
     * @param string $customerIp
     */
    public function setCustomerIp($customerIp);
}
