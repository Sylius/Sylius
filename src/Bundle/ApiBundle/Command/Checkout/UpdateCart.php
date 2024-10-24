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
class UpdateCart
{
    public function __construct(
        public readonly string $orderTokenValue,
        public readonly ?string $email = null,
        public readonly ?AddressInterface $billingAddress = null,
        public readonly ?AddressInterface $shippingAddress = null,
        public readonly ?string $couponCode = null,
    ) {
    }
}
