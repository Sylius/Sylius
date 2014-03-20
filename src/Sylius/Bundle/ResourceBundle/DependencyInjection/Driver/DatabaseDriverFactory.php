<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver;

use Sylius\Bundle\ResourceBundle\Exception\Driver\UnknownDriverException;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class DatabaseDriverFactory
{
    public static function get(
        $type = SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ContainerBuilder $container,
        $prefix
    ) {
        switch ($type) {
            case SyliusResourceBundle::DRIVER_DOCTRINE_ORM:
                return new DoctrineORMDriver($container, $prefix);
            case SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM:
                return new DoctrineODMDriver($container, $prefix);
            case SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM:
                return new DoctrinePHPCRDriver($container, $prefix);
            default:
                throw new UnknownDriverException($type);
        }
    }
}
