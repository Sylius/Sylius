<?php

declare(strict_types=1);

namespace Sylius\Component\Product\Resolver;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

interface AvailableProductOptionValuesResolverInterface
{
    /** @return ProductOptionValueInterface[] */
    public function resolve(ProductInterface $product, ProductOptionInterface $productOption): iterable;
}
