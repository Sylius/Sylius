<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Wishlist\Model;

class WishlistItem implements WishlistItemInterface
{
    /**
     * Wishlist item id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Wishlist.
     *
     * @var WishlistInterface
     */
    protected $wishlist;

    /**
     * Origin ID.
     *
     * @var int
     */
    protected $originId;

    /**
     * Origin type.
     *
     * @var string
     */
    protected $originType;

    /**
     * Bitmask for notifications.
     *
     * @var int
     */
    protected $notifyOn = 0;

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
    public function getWishlist()
    {
        return $this->wishlist;
    }

    /**
     * {@inheritdoc}
     */
    public function setWishlist(WishlistInterface $wishlist)
    {
        $this->wishlist = $wishlist;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginId()
    {
        return $this->originId;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginId($originId)
    {
        $this->originId = $originId;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginType()
    {
        return $this->originType;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginType($originType)
    {
        $this->originType = $originType;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotifyOn()
    {
        return $this->notifyOn;
    }

    /**
     * {@inheritdoc}
     */
    public function setNotifyOn($notify)
    {
        $this->notifyOn = $notify;
    }
}
