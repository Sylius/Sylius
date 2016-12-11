<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Resolver;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface AddressZoneResolverInterface
{
    /**
     * @param AddressInterface $address
     * @param ZoneInterface $zone
     *
     * @return bool
     */
    public function addressBelongsToZone(AddressInterface $address, ZoneInterface $zone);
}
