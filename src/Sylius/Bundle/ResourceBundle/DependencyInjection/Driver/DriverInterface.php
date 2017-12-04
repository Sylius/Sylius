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

use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface DriverInterface
{
    /**
     * @param ContainerBuilder $container
     * @param MetadataInterface $metadata
     */
    public function load(ContainerBuilder $container, MetadataInterface $metadata): void;

    /**
     * Returns unique name of the driver.
     *
     * @return string
     */
    public function getType(): string;
}
