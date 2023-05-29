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

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of AddressInterface
 *
 * @implements AddressFactoryInterface<T>
 */
class AddressFactory implements AddressFactoryInterface
{
    public function __construct(private FactoryInterface $decoratedFactory)
    {
    }

    public function createNew(): AddressInterface
    {
        return $this->decoratedFactory->createNew();
    }

    public function createForCustomer(CustomerInterface $customer): AddressInterface
    {
        /** @var AddressInterface $address */
        $address = $this->decoratedFactory->createNew();
        $address->setCustomer($customer);

        return $address;
    }
}
