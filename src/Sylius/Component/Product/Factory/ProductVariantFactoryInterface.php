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

namespace Sylius\Component\Product\Factory;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ProductVariantFactoryInterface extends FactoryInterface
{
    /**
     * @param ProductInterface $product
     *
     * @return ProductVariantInterface
     */
    public function createForProduct(ProductInterface $product): ProductVariantInterface;
}
