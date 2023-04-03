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

namespace Sylius\Component\Core\Provider\ProductVariantMap;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantsMapProvider implements ProductVariantsMapProviderInterface
{
    public function __construct(private ProductVariantMapProviderInterface $productVariantDataMapProvider)
    {
    }

    public function provide(ProductInterface $product, ChannelInterface $channel): array
    {
        $variantsMap = [];

        /** @var ProductVariantInterface $variant */
        foreach ($product->getEnabledVariants() as $variant) {
            $variantsMap[] = $this->productVariantDataMapProvider->provide($variant, $channel);
        }

        return $variantsMap;
    }
}
