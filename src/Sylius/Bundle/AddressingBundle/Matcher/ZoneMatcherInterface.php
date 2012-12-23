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
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
interface ZoneMatcherInterface
{
    /**
     * Returns best matching zone for given address if any.
     *
     * @param AddressInterface $address
     *
     * @return ZoneInterface|null
     */
    public function match(AddressInterface $address);
}
