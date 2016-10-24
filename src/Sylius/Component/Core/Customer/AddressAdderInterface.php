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

use Sylius\Component\Core\Model\AddressInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface AddressAdderInterface
{
    /**
     * @param AddressInterface $address
     */
    public function add(AddressInterface $address);
}
