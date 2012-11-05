<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Repository;

use Sylius\Bundle\ResourceBundle\Model\ResourceInterface;

/**
 * Resource repository interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ResourceRepositoryInterface
{
    /**
     * Finds resource by identifier.
     *
     * @param array $criteria
     *
     * @return ResourceInterface
     */
    function get(array $criteria);

    /**
     * Finds resource by criteria.
     *
     * @param array $criteria
     *
     * @return ResourceInterface
     */
    function getCollection(array $criteria = array(), array $sorting = array(), $limit = null);

    /**
     * Creates a new Pagerfanta instance to paginate the resources.
     *
     * @return PagerfantaInterface
     */
    function paginate(array $criteria = array(), array $sorting = array());

    /**
     * Get resource class name.
     *
     * @return string
     */
    function getClass();
}
