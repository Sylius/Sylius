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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of CustomerInterface
 *
 * @implements CustomerAfterCheckoutFactoryInterface<T>
 */
final class CustomerAfterCheckoutFactory implements CustomerAfterCheckoutFactoryInterface
{
    /** @param FactoryInterface<T> $baseCustomerFactory */
    public function __construct(private FactoryInterface $baseCustomerFactory)
    {
    }

    /** @inheritdoc */
    public function createNew(): CustomerInterface
    {
        return $this->baseCustomerFactory->createNew();
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
