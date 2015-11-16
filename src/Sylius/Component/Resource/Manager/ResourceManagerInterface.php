<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Manager;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ResourceManagerInterface
{
    /**
     * Save a resource.
     *
     * @param ResourceInterface $resource
     */
    public function persist(ResourceInterface $resource);

    /**
     * Delete a resource.
     *
     * @param ResourceInterface $resource
     */
    public function remove(ResourceInterface $resource);

    /**
     * Commit changes.
     */
    public function flush();

    /**
     * Get the latest object state.
     *
     * @param ResourceInterface $resource
     */
    public function refresh(ResourceInterface $resource);

    /**
     * Restore given resources.
     *
     * @param ResourceInterface $resource
     */
    public function restore(ResourceInterface $resource);
}
