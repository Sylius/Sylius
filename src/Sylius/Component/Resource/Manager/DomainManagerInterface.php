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
     * @param bool        $flush
     * @param bool        $transactional
     *
     * @return object|null
     */
    public function create($resource = null, $eventName = 'create', $flush = true, $transactional = true);

    /**
     * Update object in database.
     *
     * @param object $resource
     * @param string $eventName
     * @param bool   $flush
     * @param bool   $transactional
     *
     * @return object|null
     */
    public function update($resource, $eventName = 'update', $flush = true, $transactional = true);

    /**
     * Remove object from database.
     *
     * @param object $resource
     * @param string $eventName
     * @param bool   $flush
     * @param bool   $transactional
     *
     * @return object|null
     */
    public function delete($resource, $eventName = 'delete', $flush = true, $transactional = true);
}
