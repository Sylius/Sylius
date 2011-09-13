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
 * Model for carts.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class Cart implements CartInterface
{   
    /**
 	 * Id.
 	 * 
 	 * @var integer
     */
    protected $id;
    
    /**
     * Items in cart.
     * 
     * @var array
     */
    protected $items;
    
    /**
     * Hash.
     * 
     * @var string
     */
    protected $hash;
    
    /**
     * Total items count.
     * 
     * @var integer
     */
    protected $totalItems;
    
    /**
     * Expiration time.
     * 
     * @var \DateTime
     */
    protected $expiresAt;
    
    /**
     * Constructor.
     */
    public function __construct()
    {   
        $this->totalItems = 0;
        $this->generateHash();
        $this->incrementExpiresAt();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getHash()
    {
        return $this->hash;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }
    
    /**
     * {@inheritdoc}
     */
    public function generateHash()
    {
        if (null === $this->hash) {
            $bytes = false;
            if (function_exists('openssl_random_pseudo_bytes') && 0 !== stripos(PHP_OS, 'win')) {
                $bytes = openssl_random_pseudo_bytes(32, $strong);

                if (true !== $strong) {
                    $bytes = false;
                }
            }

            if (false === $bytes) {
                $bytes = hash('sha256', uniqid(mt_rand(), true), true);
            }

            $this->hash = base_convert(bin2hex($bytes), 16, 36);
        }
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
    public function isEmpty()
    {
        return 0 === $this->countItems();
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
    public function countItems()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(ItemInterface $item)
    {
        if (!$this->hasItem($item)) {
            $item->setCart($this);
            $this->items[] = $item;
        }

        return $this;
    }

   /**
     * {@inheritdoc}
     */
    public function removeItem(ItemInterface $item)
    {
        if ($this->hasItem($item)) {
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasItem(ItemInterface $item)
    {
        foreach ($this->items as $i) {
            if ($item === $i) return true;
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function clearItems()
    {
        $this->items = array();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setExpiresAt(\DateTime $expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }
    
    /**
     * {@inheritdoc}
     */
    public function incrementExpiresAt()
    {
        $expiresAt = new \DateTime();
        $expiresAt->add(new \DateInterval('PT3H'));
        
        $this->expiresAt = $expiresAt;
    }
}
