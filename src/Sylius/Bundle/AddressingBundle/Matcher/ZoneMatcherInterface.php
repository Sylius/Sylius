<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Matcher;

use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;

/**
 * Zone matcher interface.
 *
 * Service implementing this interface should be able to find
 * best matching zones for provided address model.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
interface ZoneMatcherInterface
{
    /**
     * Returns best matching zone for given address.
     *
     * @param AddressInterface $address
     *
     * @return ZoneInterface|null
     */
    public function match(AddressInterface $address);

    /**
     * Returns all matching zones for given address.
     *
     * @param AddressInterface $address
     *
     * @return ZoneInterface[]
     */
    public function matchAll(AddressInterface $address);
}
