<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Matcher;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

/**
 * Service implementing this interface should be able to find
 * best matching zones for provided address model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ZoneMatcherInterface
{
    /**
     * Returns the best matching zone for given address.
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
     * @return Collection|ZoneInterface[]
     */
    public function matchAll(AddressInterface $address);
}
