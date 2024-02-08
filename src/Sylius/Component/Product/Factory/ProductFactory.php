<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Product\Factory;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class ProductFactory implements ProductFactoryInterface
{
    public function __construct(private FactoryInterface $factory, private FactoryInterface $variantFactory)
    {
    }

    public function createNew(): ProductInterface
    {
        return $this->factory->createNew();
    }

    public function createWithVariant(): ProductInterface
    {
        $variant = $this->variantFactory->createNew();

        /** @var ProductInterface $product */
        $product = $this->factory->createNew();
        $product->addVariant($variant);

        return $product;
    }
}
