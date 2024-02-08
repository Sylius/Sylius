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

namespace Sylius\Component\Core\Customer;

use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class CustomerUniqueAddressAdder implements CustomerAddressAdderInterface
{
    public function __construct(private AddressComparatorInterface $addressComparator)
    {
    }

    public function add(CustomerInterface $customer, AddressInterface $address): void
    {
        foreach ($customer->getAddresses() as $customerAddress) {
            if ($this->addressComparator->equal($customerAddress, $address)) {
                return;
            }
        }

        $customer->addAddress($address);
    }
}
