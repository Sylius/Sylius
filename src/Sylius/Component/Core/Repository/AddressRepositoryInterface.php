<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @return AddressInterface[]
     */
    public function findByCustomer(CustomerInterface $customer);

    /**
     * @param CustomerInterface $customer
     * @param string $id
     *
     * @return AddressInterface|null
     */
    public function findOneByCustomerAndId(CustomerInterface $customer, $id);
}
