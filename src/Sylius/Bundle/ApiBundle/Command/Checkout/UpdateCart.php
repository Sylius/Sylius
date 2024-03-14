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

namespace Sylius\Bundle\ApiBundle\Command\Checkout;

use Sylius\Bundle\ApiBundle\Command\CustomerEmailAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Addressing\Model\AddressInterface;

class UpdateCart implements OrderTokenValueAwareInterface, CustomerEmailAwareInterface, LocaleCodeAwareInterface
{
    public ?string $orderTokenValue = null;

    /** @immutable */
    public ?string $email = null;

    /** @immutable */
    public ?AddressInterface $billingAddress = null;

    /** @immutable */
    public ?AddressInterface $shippingAddress = null;

    public ?string $couponCode = null;

    public ?string $localeCode = null;

    public function __construct(
        ?string $email = null,
        ?AddressInterface $billingAddress = null,
        ?AddressInterface $shippingAddress = null,
        ?string $couponCode = null,
        ?string $localeCode = null,
    ) {
        $this->email = $email;
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = $shippingAddress;
        $this->couponCode = $couponCode;
        $this->localeCode = $localeCode;
    }

    public static function createWithCouponData(?string $couponCode): self
    {
        return new self(null, null, null, $couponCode);
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getBillingAddress(): ?AddressInterface
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?AddressInterface $billingAddress): void
    {
        $this->billingAddress = $billingAddress;
    }

    public function getShippingAddress(): ?AddressInterface
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?AddressInterface $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function setCouponCode(?string $couponCode): void
    {
        $this->couponCode = $couponCode;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(?string $localeCode): void
    {
        $this->localeCode = $localeCode;
    }
}
