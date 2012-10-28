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
     * Creates a new Pagerfanta instance to paginate the resources.
     *
     * @return PagerfantaInterface
     */
    function createPaginator();

    /**
     * Persist.
     *
     * @param ResourceInterface $resource
     * @param Boolean           $flush
     */
    function persist(ResourceInterface $resource, $flush =  true);

    /**
     * Removes resource.
     *
     * @param ResourceInterface $resource
     * @param Boolean           $flush
     */
    function remove(ResourceInterface $resource, $flush = true);

    /**
     * Finds resource by id.
     *
     * @param mixed Identifier
     *
     * @return ResourceInterface
     */
    function find($id);

    /**
     * Finds resource by criteria.
     *
     * @param array $criteria
     *
     * @return ResourceInterface
     */
    function findOneBy(array $criteria);

    /**
     * Find all.
     *
     * @return Collection
     */
    function findAll();

    /**
     * Finds resources by criteria.
     *
     * @param array $criteria
     *
     * @return array
     */
    function findBy(array $criteria);

    /**
     * Get resource class name.
     *
     * @return string
     */
    function getClass();
}
