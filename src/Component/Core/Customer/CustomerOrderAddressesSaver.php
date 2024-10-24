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

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CustomerOrderAddressesSaver implements OrderAddressesSaverInterface
{
    public function __construct(private CustomerAddressAdderInterface $addressAdder)
    {
    }

    public function saveAddresses(OrderInterface $order): void
    {
        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();
        if (null === $customer->getUser()) {
            return;
        }

        $this->addAddress($customer, $order->getBillingAddress());
        $this->addAddress($customer, $order->getShippingAddress());
    }

    private function addAddress(CustomerInterface $customer, ?AddressInterface $address): void
    {
        if (null !== $address) {
            $this->addressAdder->add($customer, clone $address);
        }
    }
}
