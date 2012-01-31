<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Pawel Jedrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

/**
 * Interface for order status model manager.
 *
 * @author Pawel Jedrzejewski <pjedrzejewski@diweb.pl>
 */
interface StatusManagerInterface
{
    /**
     * Returns FQCN of order status model.
     * 
     * @return string
     */
    function getClass();
    
    /**
     * Creates status model object.
     */
    function createStatus();
    
    /**
     * Finds status by id.
     * 
     * @param integer $id
     */
    function findStatus($id);
    
    /**
     * Finds status by criteria.
     * 
     * @param array $criteria
     */
    function findStatusBy(array $criteria);
    
    /**
     * Finds all statuses.
     * 
     * @return array
     */
    function findStatuses();
    
    /**
     * Finds statuses by criteria.
     * 
     * @param array $criteria
     */
    function findStatusesBy(array $criteria);
}
