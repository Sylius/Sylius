<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Metadata;

/**
 * Registry of all resources.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ResourceRegistryInterface
{
    /**
     * Get all resources metadata.
     *
     * @return ResourceMetadataInterface[]
     */
    public function getAll();

    /**
     * Get resource metadata by its alias.
     *
     * @param string $alias
     *
     * @return ResourceMetadataInterface
     */
    public function get($alias);

    /**
     * @param string $className
     *
     * @return ResourceMetadataInterface
     */
    public function getByClass($className);

    /**
     * Adds resource.
     *
     * @param string $alias
     * @param array  $configuration
     */
    public function add($alias, array $configuration);
}
