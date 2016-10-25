<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Customer;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CustomerOrderAddressesSaver implements OrderAddressesSaverInterface
{
    /**
     * @var AddressAdderInterface
     */
    private $addressAdder;

    /**
     * @param AddressAdderInterface $addressAdder
     */
    public function __construct(AddressAdderInterface $addressAdder)
    {
        $this->addressAdder = $addressAdder;
    }

    /**
     * @param OrderInterface $order
     */
    public function saveAddresses(OrderInterface $order)
    {
        $shippingAddress = $order->getShippingAddress();
        $billingAddress = $order->getBillingAddress();

        $this->addressAdder->add(clone $billingAddress);
        $this->addressAdder->add(clone $shippingAddress);
    }
}
