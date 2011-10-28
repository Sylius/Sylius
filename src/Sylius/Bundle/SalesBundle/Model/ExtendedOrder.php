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
 * Model for orders.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class ExtendedOrder extends Order implements ExtendedOrderInterface
{   
    /**
     * Items in order.
     * 
     * @var array
     */
    protected $items;
    
    /**
     * Total items count.
     * 
     * @var integer
     */
    protected $totalItems;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->totalItems = 0;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setTotalItems($totalItems)
    {
        $this->totalItems = $totalItems;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }
    
	/**
     * {@inheritdoc}
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(ItemInterface $item)
    {
        if (!$this->hasItem($item)) {
            $this->items[] = $item;
        }

        return $this;
    }

   /**
     * {@inheritdoc}
     */
    public function removeItem(ItemInterface $item)
    {
        return $this->items->removeElement($item);
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasItem(ItemInterface $item)
    {
        return $this->items->contains($item);
    }
    
    /**
     * {@inheritdoc}
     */
    public function countItems()
    {
        return $this->items->count();
    }
    
    /**
     * {@inheritdoc}
     */
    public function clearItems()
    {
        $this->items = array();
    }
}
