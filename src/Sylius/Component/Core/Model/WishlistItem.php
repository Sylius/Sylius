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
     * Product.
     *
     * @var ProductVariantInterface
     */
    protected $product;

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
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductVariantInterface $product)
    {
        $this->product = $product;
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
