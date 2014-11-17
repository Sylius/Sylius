<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class Wishlist implements WishlistInterface
{
    /**
     * Wishlist id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Wishlist items.
     *
     * @var Collection|WishlistItemInterface[]
     */
    protected $items;

    /**
     * User.
     *
     * @var UserInterface
     */
    protected $user;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function addItem(WishlistItemInterface $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }
    }

    public function removeItem(WishlistItemInterface $item)
    {
        if ($this->items->contains($item)) {
            $this->items->remove($item);
        }
    }
}
