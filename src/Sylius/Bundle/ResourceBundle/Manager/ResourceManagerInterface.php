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
    public function create();

    /**
     * Persist.
     *
     * @param ResourceInterface $resource
     * @param Boolean           $flush
     */
    public function persist(ResourceInterface $resource, $flush =  true);

    /**
     * Removes resource.
     *
     * @param ResourceInterface $resource
     * @param Boolean           $flush
     */
    public function remove(ResourceInterface $resource, $flush = true);

    /**
     * Get resource class name.
     *
     * @return string
     */
    public function getClassName();
}
