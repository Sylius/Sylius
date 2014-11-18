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

interface WishlistItemInterface
{
    const NOTIFY_ON_PRICE_CHANGE = 1;
    const NOTIFY_ON_STOCK_CHANGE = 2;

    /**
     * @return WishlistInterface
     */
    public function getWishlist();

    /**
     * @param WishlistInterface $wishlist
     */
    public function setWishlist(WishlistInterface $wishlist);

    /**
     * @return ProductVariantInterface
     */
    public function getVariant();

    /**
     * @param ProductVariantInterface $variant
     */
    public function setVariant(ProductVariantInterface $variant);

    /**
     * @return int
     */
    public function getNotifyOn();

    /**
     * @param int $notify
     */
    public function setNotifyOn($notify);
}
