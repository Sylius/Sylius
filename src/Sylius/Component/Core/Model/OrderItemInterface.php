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

use Sylius\Component\Cart\Model\CartItemInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderItemInterface extends CartItemInterface
{
    /**
     * @return ProductInterface
     */
    public function getProduct();

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
    public function getTaxTotal();

    /**
     * @return int
     */
    public function getDiscountedUnitPrice();

    /**
     * @return int
     */
    public function getSubtotal();
}
