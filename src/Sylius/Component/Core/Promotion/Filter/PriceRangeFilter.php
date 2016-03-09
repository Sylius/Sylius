<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Filter;

use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PriceRangeFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(array $items, array $configuration)
    {
        if ($this->isInvalidConfiguration($configuration)) {
            return $items;
        }

        $filteredItems = [];
        foreach ($items as $item) {
            if ($this->isItemVariantInPriceRange($item->getVariant(), $configuration['filters']['price_range'])) {
                $filteredItems[] = $item;
            }
        }

        return $filteredItems;
    }

    /**
     * @param ProductVariantInterface $variant
     * @param array $priceRange
     *
     * @return bool
     */
    private function isItemVariantInPriceRange(ProductVariantInterface $variant, array $priceRange)
    {
        $price = $variant->getPrice();

        if (isset($priceRange['min']) && isset($priceRange['max'])) {
            return $priceRange['min'] <= $price && $priceRange['max'] >= $price;
        }

        if (isset($priceRange['min']) && !isset($priceRange['max'])) {
            return $priceRange['min'] <= $price;
        }

        return $priceRange['max'] >= $price;
    }

    /**
     * @param array $configuration
     *
     * @return bool
     */
    private function isInvalidConfiguration(array $configuration)
    {
        return
            !isset($configuration['filters']['price_range']['min']) ||
            !isset($configuration['filters']['price_range']['max'])
        ;
    }
}
