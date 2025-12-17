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

namespace Sylius\Component\Core\Provider\ProductVariantMap;

use Sylius\Component\Core\Model\ProductVariantInterface;

interface ProductVariantMapProviderInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function provide(ProductVariantInterface $variant, array $context): array;

    /** @param array<string, mixed> $context */
    public function supports(ProductVariantInterface $variant, array $context): bool;
}
