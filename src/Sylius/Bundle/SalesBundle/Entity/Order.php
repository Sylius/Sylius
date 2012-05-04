<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\SalesBundle\Model\ItemInterface;
use Sylius\Bundle\SalesBundle\Model\Order as BaseOrder;

/**
 * Base order entity.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class Order extends BaseOrder
{
    /**
     * Override constructor to initialize collections.
     */
    public function __construct()
    {
        parent::__construct();

        $this->items = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(ItemInterface $item)
    {
        if (!$this->hasItem($item)) {
            $item->setOrder($this);
            $this->items->add($item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(ItemInterface $item)
    {
        if ($this->hasItem($item)) {
            $item->setOrder(null);
            $this->items->removeElement($item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem(ItemInterface $item)
    {
        return $this->items->contains($item);
    }
}
