<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Flexible inventory management for Symfony2.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusInventoryBundle extends Bundle
{
    // Bundle driver list.
    const DRIVER_DOCTRINE_ORM = 'doctrine/orm';
    const DRIVER_PROPEL       = 'propel';
    const DRIVER_PROPEL2      = 'propel2';

    /**
     * Return array with currently supported drivers.
     *
     * @return array
     */
    static public function getSupportedDrivers()
    {
        return array(
            self::DRIVER_DOCTRINE_ORM
        );
    }
}
