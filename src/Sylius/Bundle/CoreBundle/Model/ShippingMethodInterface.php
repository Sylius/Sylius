<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface as BaseShippingMethodInterface;

/**
 * Shipping method interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ShippingMethodInterface extends BaseShippingMethodInterface
{
    /**
     * Get zone.
     *
     * @return ZoneInterface
     */
    public function getZone();

    /**
     * Set zone.
     *
     * @param ZoneInterface $zone
     */
    public function setZone(ZoneInterface $zone);
}
