<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Repository;

/**
 * Model repository interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ResourceRepositoryInterface
{
    /**
     * Find the resource by id.
     *
     * @param mixed $id
     *
     * @return null|ResourceInterface
     */
    public function find($id);

    /**
     * Find all resources.
     *
     * @return array
     */
    public function findAll();

    /**
     * Find resources by specific criteria.
     *
     * @param array        $criteria
     * @param array|null   $orderBy
     * @param integer      $limit
     * @param integer|null $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Find resource by specific criteria.
     *
     * @param array $criteria
     */
    public function findOneBy(array $criteria);

    /**
     * Get paginated collection.
     *
     * @param array $criteria
     * @param array $orderBy
     *
     * @return PagerfantaInterface
     */
    public function createPaginator(array $criteria = null, array $orderBy = null);

    /**
     * Enable a filter.
     *
     * @param string $name
     */
    public function enableFilter($name);

    /**
     * Disable a filter.
     *
     * @param string $name
     */
    public function disableFilter($name);
}
