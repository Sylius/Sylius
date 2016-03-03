<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Product;

use Sylius\Behat\Page\ElementNotFoundException;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProductShowPageInterface
{
    /**
     * @throws ElementNotFoundException
     */
    public function addToCart();

    /**
     * @param string $quantity
     */
    public function addToCartWithQuantity($quantity);

    /**
     * @param string $variant
     */
    public function addToCartWithVariant($variant);
}
