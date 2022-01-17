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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CustomerAfterCheckoutFactory implements CustomerAfterCheckoutFactoryInterface
{
    public function __construct(private FactoryInterface $baseCustomerFactory)
    {
    }

    public function createNew(): CustomerInterface
    {
        /** @var CustomerInterface $customer */
        $customer = $this->baseCustomerFactory->createNew();

        return $customer;
    }

    public function createAfterCheckout(OrderInterface $order): CustomerInterface
    {
        $guest = $order->getCustomer();
        $address = $order->getBillingAddress();

        $customer = $this->createNew();
        $customer->setEmail($guest->getEmail());
        $customer->setFirstName($address->getFirstName());
        $customer->setLastName($address->getLastName());
        $customer->setPhoneNumber($address->getPhoneNumber());

        return $customer;
    }
}
