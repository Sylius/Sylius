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

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Templating\Helper\Helper;

class VariantResolverHelper extends Helper
{
    public function __construct(private ProductVariantResolverInterface $productVariantResolver)
    {
    }

    public function resolveVariant(ProductInterface $product): ?ProductVariantInterface
    {
        return $this->productVariantResolver->getVariant($product);
    }

    public function getName(): string
    {
        return 'sylius_resolve_variant';
    }
}
