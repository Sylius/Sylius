<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

interface BillingAddressAwareInterface
{
    /**
     * @return AddressInterface
     */
    public function getBillingAddress();

    /**
     * @param AddressInterface $address
     */
    public function setBillingAddress(AddressInterface $address = null);
}
