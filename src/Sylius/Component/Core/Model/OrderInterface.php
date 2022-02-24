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
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Payment\Model\PaymentsSubjectInterface;
use Sylius\Component\Promotion\Model\CountablePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponAwarePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface as BaseCouponInterface;
use Sylius\Component\User\Model\UserInterface;

interface OrderInterface extends
    BaseOrderInterface,
    PaymentsSubjectInterface,
    CountablePromotionSubjectInterface,
    PromotionCouponAwarePromotionSubjectInterface,
    CustomerAwareInterface,
    ChannelAwareInterface
{
    public function getUser(): ?UserInterface;

    public function getShippingAddress(): ?AddressInterface;

    public function setShippingAddress(?AddressInterface $address): void;

    public function getBillingAddress(): ?AddressInterface;

    public function setBillingAddress(?AddressInterface $address): void;

    public function getCheckoutState(): ?string;

    public function setCheckoutState(?string $checkoutState): void;

    public function getPaymentState(): ?string;

    public function setPaymentState(?string $paymentState): void;

    /**
     * @return Collection|OrderItemUnitInterface[]
     *
     * @psalm-return Collection<array-key, OrderItemUnitInterface>
     */
    public function getItemUnits(): Collection;

    /**
     * @return Collection|OrderItemUnitInterface[]
     *
     * @psalm-return Collection<array-key, OrderItemUnitInterface>
     */
    public function getItemUnitsByVariant(ProductVariantInterface $variant): Collection;

    public function isShippingRequired(): bool;

    /**
     * @return Collection|ShipmentInterface[]
     *
     * @psalm-return Collection<array-key, ShipmentInterface>
     */
    public function getShipments(): Collection;

    public function hasShipments(): bool;

    public function addShipment(ShipmentInterface $shipment): void;

    public function removeShipment(ShipmentInterface $shipment): void;

    public function removeShipments(): void;

    public function hasShipment(ShipmentInterface $shipment): bool;

    public function getCurrencyCode(): ?string;

    public function setCurrencyCode(?string $currencyCode): void;

    public function getLocaleCode(): ?string;

    public function setLocaleCode(?string $localeCode): void;

    public function setPromotionCoupon(?BaseCouponInterface $coupon): void;

    public function getShippingState(): ?string;

    public function setShippingState(?string $state): void;

    public function getLastPayment(?string $state = null): ?PaymentInterface;

    public function getTaxTotal(): int;

    public function getShippingTotal(): int;

    public function getOrderPromotionTotal(): int;

    public function getTokenValue(): ?string;

    public function setTokenValue(?string $tokenValue): void;

    public function getCustomerIp(): ?string;

    public function setCustomerIp(?string $customerIp): void;

    public function getByGuest(): bool;

    public function setByGuest(bool $guest): void;

    /**
     * @return Collection|OrderItemInterface[]
     *
     * @psalm-return Collection<array-key, OrderItemInterface>
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     */
    public function getItems(): Collection;

    /**
     * @return ChannelInterface|null
     */
    public function getChannel(): ?BaseChannelInterface;
}
