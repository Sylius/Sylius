<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

/**
 * Order status manager interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface StatusManagerInterface
{
    /**
     * Returns status model class.
     * FQCN.
     *
     * @return string
     */
    function getClass();

    /**
     * Creates new status model object.
     *
     * @return StatusInterface
     */
    function createStatus();

    /**
     * Saves status.
     *
     * @param StatusInterface $status
     */
    function persistStatus(StatusInterface $status);

    /**
     * Removes status.
     *
     * @param StatusInterface $status
     */
    function removeStatus(StatusInterface $status);

    /**
     * Finds status by id.
     *
     * @param mixed $id
     *
     * @return StatusInterface
     */
    function findStatus($id);

    /**
     * Finds status by given criteria.
     *
     * @param array $criteria
     *
     * @return StatusInterface
     */
    function findStatusBy(array $criteria);

    /**
     * Finds all statuses.
     * They should be ordered by position.
     *
     * @return array
     */
    function findStatuses();

    /**
     * Finds statuses by criteria.
     *
     * @return array
     */
    function findStatusesBy(array $criteria);
}
