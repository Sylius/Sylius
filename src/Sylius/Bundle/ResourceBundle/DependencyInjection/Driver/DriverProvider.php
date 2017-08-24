<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrineODMDriver;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrineORMDriver;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrinePHPCRDriver;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Exception\UnknownDriverException;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Metadata\MetadataInterface;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
final class DriverProvider
{
    /**
     * @var DriverInterface[]
     */
    private static $drivers = [];

    /**
     * @param MetadataInterface $metadata
     *
     * @return DriverInterface
     *
     * @throws UnknownDriverException
     */
    public static function get(MetadataInterface $metadata): DriverInterface
    {
        $type = $metadata->getDriver();

        if (isset(self::$drivers[$type])) {
            return self::$drivers[$type];
        }

        switch ($type) {
            case SyliusResourceBundle::DRIVER_DOCTRINE_ORM:
                return self::$drivers[$type] = new DoctrineORMDriver();
            case SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM:
                return self::$drivers[$type] = new DoctrineODMDriver();
            case SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM:
                return self::$drivers[$type] = new DoctrinePHPCRDriver();
        }

        throw new UnknownDriverException($type);
    }
}
