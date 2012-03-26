<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\CartBundle\Model\Cart as BaseCart;
use Sylius\Bundle\CartBundle\Model\ItemInterface;

/**
 * Cart entity.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Cart extends BaseCart
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->items = new ArrayCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(ItemInterface $item)
    {
        if (!$this->hasItem($item)) {
            $this->items->add($item);
            $item->setCart($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(ItemInterface $item)
    {
        if ($this->hasItem($item)) {
            $this->items->removeElement($item);
            $item->setCart(null);
        }
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
    public function clearItems()
    {
        $this->items->clear();
    }
}
