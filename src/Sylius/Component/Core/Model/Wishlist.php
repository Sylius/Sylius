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
     * Name of saved wishlist.
     *
     * @var string
     */
    protected $name;

    /**
     * Permalink for the wishlist.
     * Used in url to access it.
     *
     * @var string
     */
    protected $slug;

    /**
     * Is wishlist visible for others?
     *
     * @var bool
     */
    protected $public = false;

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

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTime();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug = null)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublic($public)
    {
        $this->public = (bool) $public;
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
    public function addItem(WishlistItemInterface $item)
    {
        $items = $this->items->filter(function (WishlistItemInterface $existingItem) use ($item) {
            return $existingItem->getVariant()->getId() === $item->getVariant()->getId();
        });

        if ($items->isEmpty()) {
            $this->items->add($item);
            $item->setWishlist($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(WishlistItemInterface $item)
    {
        $items = $this->items->filter(function (WishlistItemInterface $existingItem) use ($item) {
            return $existingItem->getVariant()->getId() === $item->getVariant()->getId();
        });

        if (!$items->isEmpty()) {
            $this->items->removeElement($items->current());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
