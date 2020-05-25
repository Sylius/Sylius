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

namespace Sylius\Component\Product\Resolver;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class AvailableProductOptionValuesResolver implements AvailableProductOptionValuesResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve(ProductInterface $product, ProductOptionInterface $productOption): Collection
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
