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

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantsMapProvider implements ProductVariantsMapProviderInterface
{
    /** @param iterable<ProductVariantMapProviderInterface> $dataMapProviders */
    public function __construct(private iterable $dataMapProviders)
    {
    }

    public function provide(ProductInterface $product, array $context): array
    {
        $variantsMap = [];

        /** @var ProductVariantInterface $variant */
        foreach ($product->getEnabledVariants() as $variant) {
            $variantsMap[] = $this->getMapForVariant($variant, $context);
        }

        return $variantsMap;
    }

    private function getMapForVariant(ProductVariantInterface $variant, array $context): array
    {
        $data = [];

        foreach ($this->dataMapProviders as $dataMapProvider) {
            if ($dataMapProvider->supports($variant, $context)) {
                $data += $dataMapProvider->provide($variant, $context);
            }
        }

        return $data;
    }
}
