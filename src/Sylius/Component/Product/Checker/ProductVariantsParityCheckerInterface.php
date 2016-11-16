<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Checker;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProductVariantsParityCheckerInterface
{
    /**
     * @param ProductVariantInterface $variant
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function checkParity(ProductVariantInterface $variant, ProductInterface $product);
}
