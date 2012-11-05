<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Manager;

use Sylius\Bundle\ResourceBundle\Model\ResourceInterface;

/**
 * Resource manager interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ResourceManagerInterface
{
    /**
     * Creates new resource object.
     *
     * @return ResourceInterface
     */
    function create();

    /**
     * Persist.
     *
     * @param ResourceInterface $resource
     * @param Boolean           $commit
     */
    function persist(ResourceInterface $resource, $commit =  true);

    /**
     * Removes resource.
     *
     * @param ResourceInterface $resource
     * @param Boolean           $commit
     */
    function remove(ResourceInterface $resource, $commit = true);

    /**
     * Get resource class name.
     *
     * @return string
     */
    function getClass();
}
