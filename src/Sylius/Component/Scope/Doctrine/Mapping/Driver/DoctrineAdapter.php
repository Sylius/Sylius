<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Scope\Doctrine\Mapping\Driver;


use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver as DoctrineAnnotationDriver;
use Doctrine\Common\Persistence\Mapping\Driver\FileDriver as DoctrineFileDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Metadata\Driver\DriverChain;
use Metadata\Driver\DriverInterface;

/**
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
 */
class DoctrineAdapter
{
    /**
     * Create a driver from doctrine registry
     *
     * @param ManagerRegistry $registry
     * @return DriverInterface
     */
    public static function fromRegistry(ManagerRegistry $registry)
    {
        $drivers = array();
        foreach ($registry->getManagers() as $manager) {
            $drivers[] = self::fromManager($manager);
        }

        return new DriverChain($drivers);
    }

    /**
     * Create a driver from doctrine object manager
     *
     * @param ObjectManager $om
     * @return DriverInterface
     */
    public static function fromManager(ObjectManager $om)
    {
        return self::fromMetadataDriver($om->getConfiguration()->getMetadataDriverImpl());
    }

    /**
     * Create a driver from a doctrine metadata driver
     *
     * @param MappingDriver $omDriver
     * @return DriverInterface
     */
    public static function fromMetadataDriver(MappingDriver $omDriver)
    {
        if ($omDriver instanceof MappingDriverChain) {
            $drivers = array();
            foreach ($omDriver->getDrivers() as $nestedOmDriver) {
                $drivers[] = self::fromMetadataDriver($nestedOmDriver);
            }

            return new DriverChain($drivers);
        }

        if ($omDriver instanceof DoctrineAnnotationDriver) {
            return new AnnotationDriver($omDriver->getReader());
        }

        if ($omDriver instanceof DoctrineFileDriver) {
            $reflClass = new \ReflectionClass($omDriver);
            $driverName = $omDriver instanceof SimplifiedYamlDriver ? 'YamlDriver' : $reflClass->getShortName();
            $class = 'Sylius\\Component\\Scope\\Doctrine\\Mapping\\Driver\\' . $driverName;

            if (class_exists($class)) {
                return new $class($omDriver->getLocator());
            }
        }

        throw new \InvalidArgumentException('Cannot adapt Doctrine driver of class ' . get_class($omDriver));
    }
}