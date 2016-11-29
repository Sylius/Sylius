<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface AddressFactoryInterface extends FactoryInterface
{
    /**
     * @param CustomerInterface $customer
     * 
     * @return AddressInterface 
     */
    public function createForCustomer(CustomerInterface $customer);
}
