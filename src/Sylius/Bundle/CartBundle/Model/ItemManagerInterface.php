<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Model;

/**
 * Interface for cart intem model manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ItemManagerInterface
{
    /**
     * Returns FQCN of cart item model.
     * 
     * @return string
     */
    function getClass();
    
    /**
     * Creates item model object.
     */
    function createItem();
    
    /**
     * Persists item.
     * 
     * @param ItemInterface $item
     */
    function persistItem(ItemInterface $item);
    
    /**
     * Removes item.
     * 
     * @param ItemInterface $item
     */
    function removeItem(ItemInterface $item);
    
    /**
     * Finds item by id.
     * 
     * @param integer $id
     */
    function findItem($id);
    
    /**
     * Finds item by criteria.
     * 
     * @param array $criteria
     */
    function findItemBy(array $criteria);
    
    /**
     * Finds all items.
     * 
     * @return array
     */
    function findItems();
    
    /**
     * Finds items by criteria.
     * 
     * @param array $criteria
     */
    function findItemsBy(array $criteria);
}
