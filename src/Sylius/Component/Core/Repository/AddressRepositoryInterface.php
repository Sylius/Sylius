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

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of AddressInterface
 *
 * @extends RepositoryInterface<T>
 */
interface AddressRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array|AddressInterface[]
     */
    public function findByCustomer(CustomerInterface $customer): array;

    public function findOneByCustomer(string $id, CustomerInterface $customer): ?AddressInterface;
}
