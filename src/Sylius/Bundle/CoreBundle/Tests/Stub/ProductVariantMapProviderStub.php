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

namespace Sylius\Bundle\CoreBundle\Tests\Stub;

use Sylius\Bundle\CoreBundle\Attribute\AsProductVariantMapProvider;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface;

#[AsProductVariantMapProvider(priority: 4)]
final class ProductVariantMapProviderStub implements ProductVariantMapProviderInterface
{
    public function provide(ProductVariantInterface $variant, array $context): array
    {
        return [];
    }

    public function supports(ProductVariantInterface $variant, array $context): bool
    {
        return true;
    }
}
