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

namespace Sylius\Component\Core\Promotion\Filter;

use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class PriceRangeFilter implements FilterInterface
{
    public function __construct(private ProductVariantPriceCalculatorInterface $productVariantPriceCalculator)
    {
    }

    public function filter(array $items, array $configuration): array
    {
        if (!$this->isConfigured($configuration)) {
            return $items;
        }

        Assert::keyExists($configuration, 'channel');

        $filteredItems = [];
        foreach ($items as $item) {
            if ($this->isItemVariantInPriceRange($item->getVariant(), $configuration)) {
                $filteredItems[] = $item;
            }
        }

        return $filteredItems;
    }

    private function isItemVariantInPriceRange(ProductVariantInterface $variant, array $configuration): bool
    {
        $price = $this->productVariantPriceCalculator->calculate($variant, ['channel' => $configuration['channel']]);

        $priceRange = $configuration['filters']['price_range_filter'];
        if (isset($priceRange['min'], $priceRange['max'])) {
            return $priceRange['min'] <= $price && $priceRange['max'] >= $price;
        }

        return $priceRange['min'] <= $price;
    }

    private function isConfigured(array $configuration): bool
    {
        return isset($configuration['filters']['price_range_filter']['min']);
    }
}
