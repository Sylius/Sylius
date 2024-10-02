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

use Sylius\Bundle\ApiBundle\Attribute\LoggedInCustomerEmailAware;
use Sylius\Bundle\ApiBundle\Attribute\OrderTokenValueAware;
use Sylius\Component\Addressing\Model\AddressInterface;

#[OrderTokenValueAware]
#[LoggedInCustomerEmailAware]
readonly class UpdateCart
{
    public function __construct(
        public string $orderTokenValue,
        public ?string $email = null,
        public ?AddressInterface $billingAddress = null,
        public ?AddressInterface $shippingAddress = null,
        public ?string $couponCode = null,
    ) {
    }
}
