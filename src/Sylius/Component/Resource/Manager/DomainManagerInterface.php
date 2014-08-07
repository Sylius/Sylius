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

interface DomainManagerInterface
{
    /**
     * Initialize object class.
     *
     * @return object
     */
    public function createNew();

    /**
     * Create object in database.
     *
     * @param null|object $resource
     * @param string      $eventName
     *
     * @return object|null
     */
    public function create($resource = null, $eventName = 'create');

    /**
     * Update object in database.
     *
     * @param object $resource
     * @param string $eventName
     *
     * @return object|null
     */
    public function update($resource, $eventName = 'update');

    /**
     * Remove object from database.
     *
     * @param object $resource
     * @param string $eventName
     *
     * @return object|null
     */
    public function delete($resource, $eventName = 'delete');

    /**
     * Bulk action (create/update/delete).
     *
     * @param string   $action
     * @param object[] $resources
     *
     * @return object[]
     *
     * @throws \InvalidArgumentException
     */
    public function bulk($action = 'create', array $resources);
}
