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

use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LoggedInCustomerEmailAwareInterface;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Addressing\Model\AddressInterface;

class UpdateCart implements OrderTokenValueAwareInterface, LoggedInCustomerEmailAwareInterface, LocaleCodeAwareInterface
{
    public function __construct(
        public ?string $email = null,
        public ?AddressInterface $billingAddress = null,
        public ?AddressInterface $shippingAddress = null,
        public ?string $couponCode = null,
        public ?string $localeCode = null,
        public ?string $orderTokenValue = null,
    ) {
    }

    public static function createWithCouponData(?string $couponCode): self
    {
        return new self(null, null, null, $couponCode);
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getBillingAddress(): ?AddressInterface
    {
        return $this->billingAddress;
    }

    public function getShippingAddress(): ?AddressInterface
    {
        return $this->shippingAddress;
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }
}
