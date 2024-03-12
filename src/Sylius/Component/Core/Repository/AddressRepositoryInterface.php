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

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Addressing\Model\AddressInterface as BaseAddressInterface;
use Sylius\Component\Addressing\Repository\AddressRepositoryInterface as BaseAddressRepositoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * @extends BaseAddressRepositoryInterface<BaseAddressInterface>
 */
interface AddressRepositoryInterface extends BaseAddressRepositoryInterface
{
    /**
     * @return array|AddressInterface[]
     */
    public function findByCustomer(CustomerInterface $customer): array;

    public function findOneByCustomer(string $id, CustomerInterface $customer): ?AddressInterface;
}
