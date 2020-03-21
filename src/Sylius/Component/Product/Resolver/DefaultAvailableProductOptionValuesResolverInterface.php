<?php

declare(strict_types=1);

namespace Sylius\Component\Product\Resolver;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

class DefaultAvailableProductOptionValuesResolverInterface implements AvailableProductOptionValuesResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(ProductInterface $product, ProductOptionInterface $productOption): iterable
    {
        if (!$product->hasOption($productOption)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cannot resolve available product option values. Option "%s" does not belong to product "%s".',
                    $product->getCode(),
                    $productOption->getCode()
                )
            );
        }
        return $productOption->getValues()->filter(
            static function (ProductOptionValueInterface $productOptionValue) use ($product) {
                foreach ($product->getEnabledVariants() as $productVariant) {
                    if ($productVariant->hasOptionValue($productOptionValue)) {
                        return true;
                    }
                }
                return false;
            }
        );
    }
}
