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
 * Interface for order item model manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ItemManagerInterface
{
    /**
     * Returns FQCN of order item model.
     *
     * @return string
     */
    function getClass();

    /**
     * Creates item model object.
     *
     * @return ItemInterface
     */
    function createItem();

    /**
     * Finds item by id.
     *
     * @param integer $id
     *
     * @return ItemInterface
     */
    function findItem($id);

    /**
     * Finds item by criteria.
     *
     * @param array $criteria
     *
     * @return ItemInterface
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
     *
     * @return array
     */
    function findItemsBy(array $criteria);
}
