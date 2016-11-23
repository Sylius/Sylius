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

use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PriceRangeFilter implements FilterInterface
{
    /**
     * @var ProductVariantPriceCalculatorInterface
     */
    private $productVariantPriceCalculator;

    /**
     * @param ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
     */
    public function __construct(ProductVariantPriceCalculatorInterface $productVariantPriceCalculator)
    {
        $this->productVariantPriceCalculator = $productVariantPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(array $items, array $configuration)
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

    /**
     * @param ProductVariantInterface $variant
     * @param array $configuration
     *
     * @return bool
     */
    private function isItemVariantInPriceRange(ProductVariantInterface $variant, array $configuration)
    {
        $price = $this->productVariantPriceCalculator->calculate($variant, $configuration['channel']);

        $priceRange = $configuration['filters']['price_range_filter'];
        if (isset($priceRange['min']) && isset($priceRange['max'])) {
            return $priceRange['min'] <= $price && $priceRange['max'] >= $price;
        }

        return $priceRange['min'] <= $price;
    }

    /**
     * @param array $configuration
     *
     * @return bool
     */
    private function isConfigured(array $configuration)
    {
        return isset($configuration['filters']['price_range_filter']['min']);
    }
}
