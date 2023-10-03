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

namespace Sylius\Component\Product\Resolver;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

final class CompositeProductVariantResolver implements ProductVariantResolverInterface
{
    /**
     * @param ProductVariantResolverInterface[] $productVariantResolvers
     */
    public function __construct(private iterable $productVariantResolvers)
    {
    }

    public function getVariant(ProductInterface $subject): ?ProductVariantInterface
    {
        foreach ($this->productVariantResolvers as $resolver) {
            $variant = $resolver->getVariant($subject);
            if (null !== $variant) {
                return $variant;
            }
        }

        return null;
    }
}
