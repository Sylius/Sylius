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
     * Product variant.
     *
     * @var ProductVariantInterface
     */
    protected $variant;

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
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariant(ProductVariantInterface $variant)
    {
        $this->variant = $variant;
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
