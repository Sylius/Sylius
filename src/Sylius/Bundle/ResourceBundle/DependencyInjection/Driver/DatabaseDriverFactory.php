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

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Exception\Driver\UnknownDriverException;
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class DatabaseDriverFactory
{
    /**
     * @param ResourceMetadataInterface $metadata
     *
     * @return DoctrineODMDriver|DoctrineORMDriver|DoctrinePHPCRDriver
     *
     * @throws UnknownDriverException
     */
    public static function getForResource(ResourceMetadataInterface $metadata)
    {
        switch ($metadata->getDriver()) {
            case SyliusResourceBundle::DRIVER_DOCTRINE_ORM:
                return new DoctrineORMDriver();
            case SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM:
                return new DoctrineODMDriver();
            case SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM:
                return new DoctrinePHPCRDriver();
            default:
                throw new UnknownDriverException($metadata);
        }
    }
}
