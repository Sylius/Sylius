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
     * @return object|null
     */
    public function create();

    /**
     * Update object in database.
     *
     * @param object $resource
     *
     * @return object|null
     */
    public function update($resource);

    /**
     * Remove object from database.
     *
     * @param object $resource
     *
     * @return object|null
     */
    public function delete($resource);
}
