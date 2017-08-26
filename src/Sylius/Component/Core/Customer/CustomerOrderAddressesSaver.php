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

namespace Sylius\Component\Core\Customer;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CustomerOrderAddressesSaver implements OrderAddressesSaverInterface
{
    /**
     * @var CustomerAddressAdderInterface
     */
    private $addressAdder;

    /**
     * @param CustomerAddressAdderInterface $addressAdder
     */
    public function __construct(CustomerAddressAdderInterface $addressAdder)
    {
        $this->addressAdder = $addressAdder;
    }

    /**
     * @param OrderInterface $order
     */
    public function saveAddresses(OrderInterface $order)
    {
        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();
        if (null === $customer->getUser()) {
            return;
        }

        $shippingAddress = $order->getShippingAddress();
        $billingAddress = $order->getBillingAddress();

        $this->addressAdder->add($customer, clone $billingAddress);
        $this->addressAdder->add($customer, clone $shippingAddress);
    }
}
