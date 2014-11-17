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
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * @return int
     */
    public function getNotifyOn();

    /**
     * @param int $notify
     */
    public function setNotifyOn($notify);
}
