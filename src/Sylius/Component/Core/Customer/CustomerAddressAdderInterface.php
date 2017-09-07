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

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface CustomerAddressAdderInterface
{
    /**
     * @param CustomerInterface $customer
     * @param AddressInterface $address
     */
    public function add(CustomerInterface $customer, AddressInterface $address): void;
}
