<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Product\Checker;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

interface ProductVariantsParityCheckerInterface
{
    /**
     * @param ProductVariantInterface $variant
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function checkParity(ProductVariantInterface $variant, ProductInterface $product): bool;
}
