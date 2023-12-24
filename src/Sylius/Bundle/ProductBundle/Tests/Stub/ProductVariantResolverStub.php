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

namespace Sylius\Bundle\ProductBundle\Tests\Stub;

use Sylius\Bundle\ProductBundle\Attribute\AsProductVariantResolver;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

#[AsProductVariantResolver(priority: 50)]
final class ProductVariantResolverStub implements ProductVariantResolverInterface
{
    public function getVariant(ProductInterface $subject): ?ProductVariantInterface
    {
        return null;
    }
}
