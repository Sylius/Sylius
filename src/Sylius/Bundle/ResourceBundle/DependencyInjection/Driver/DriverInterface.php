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

use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Resource driver interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface DriverInterface
{
    /**
     * Loads all services definitions for given resource.
     *
     * @param ContainerBuilder          $container
     * @param ResourceMetadataInterface $metadata
     */
    public function load(ContainerBuilder $container, ResourceMetadataInterface $metadata);

    /**
     * Return name of the driver.
     *
     * @return string
     */
    public function getName();
}
