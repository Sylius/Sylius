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
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * @return AddressInterface|null
     */
    public function getShippingAddress(): ?AddressInterface;

    /**
     * @param AddressInterface|null $address
     */
    public function setShippingAddress(?AddressInterface $address): void;

    /**
     * @return AddressInterface|null
     */
    public function getBillingAddress(): ?AddressInterface;

    /**
     * @param AddressInterface|null $address
     */
    public function setBillingAddress(?AddressInterface $address): void;

    /**
     * @return string|null
     */
    public function getCheckoutState(): ?string;

    /**
     * @param string|null $checkoutState
     */
    public function setCheckoutState(?string $checkoutState): void;

    /**
     * @return string|null
     */
    public function getPaymentState(): ?string;

    /**
     * @param string|null $paymentState
     */
    public function setPaymentState(?string $paymentState): void;

    /**
     * @return Collection|OrderItemUnitInterface[]
     */
    public function getItemUnits(): Collection;

    /**
     * @param ProductVariantInterface $variant
     *
     * @return Collection|OrderItemUnitInterface[]
     */
    public function getItemUnitsByVariant(ProductVariantInterface $variant): Collection;

    /**
     * @return bool
     */
    public function isShippingRequired(): bool;

    /**
     * @return Collection|ShipmentInterface[]
     */
    public function getShipments(): Collection;

    /**
     * @return bool
     */
    public function hasShipments(): bool;

    /**
     * @param ShipmentInterface $shipment
     */
    public function addShipment(ShipmentInterface $shipment): void;

    /**
     * @param ShipmentInterface $shipment
     */
    public function removeShipment(ShipmentInterface $shipment): void;

    public function removeShipments(): void;

    /**
     * @param ShipmentInterface $shipment
     *
     * @return bool
     */
    public function hasShipment(ShipmentInterface $shipment): bool;

    /**
     * @return string|null
     */
    public function getCurrencyCode(): ?string;

    /**
     * @param string|null $currencyCode
     */
    public function setCurrencyCode(?string $currencyCode): void;

    /**
     * @return string|null
     */
    public function getLocaleCode(): ?string;

    /**
     * @param string|null
     */
    public function setLocaleCode(?string $localeCode): void;

    /**
     * @param BaseCouponInterface|null $coupon
     */
    public function setPromotionCoupon(?BaseCouponInterface $coupon): void;

    /**
     * @return string|null
     */
    public function getShippingState(): ?string;

    /**
     * @param string|null $state
     */
    public function setShippingState(?string $state): void;

    /**
     * @param string|null $state
     *
     * @return PaymentInterface|null
     */
    public function getLastPayment(?string $state = null): ?PaymentInterface;

    /**
     * @return int
     */
    public function getTaxTotal(): int;

    /**
     * @return int
     */
    public function getShippingTotal(): int;

    /**
     * @return int
     */
    public function getOrderPromotionTotal(): int;

    /**
     * @return string|null
     */
    public function getTokenValue(): ?string;

    /**
     * @param string|null $tokenValue
     */
    public function setTokenValue(?string $tokenValue): void;

    /**
     * @return string|null
     */
    public function getCustomerIp(): ?string;

    /**
     * @param string|null $customerIp
     */
    public function setCustomerIp(?string $customerIp): void;
}
