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
 * @author Anna Walasek <anna.walasek@lakion.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface AddressRepositoryInterface extends RepositoryInterface
{
    /**
     * @param CustomerInterface $customer
     *
     * @return array|AddressInterface[]
     */
    public function findByCustomer(CustomerInterface $customer): array;

    /**
     * @param string $id
     * @param CustomerInterface $customer
     *
     * @return AddressInterface|null
     */
    public function findOneByCustomer(string $id, CustomerInterface $customer): ?AddressInterface;
}
