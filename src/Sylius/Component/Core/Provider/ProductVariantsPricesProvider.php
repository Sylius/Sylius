<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\OptionValueInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductVariantsPricesProvider implements ProductVariantsPricesProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function provideVariantsPrices(ProductInterface $product)
    {
        $variantsPrices = [];

        /** @var ProductVariantInterface $variant */
        foreach ($product->getVariants() as $variant) {
            $variantsPrices[] = $this->constructOptionsMap($variant);
        }

        return $variantsPrices;
    }

    /**
     * @param ProductVariantInterface $variant
     *
     * @return array
     */
    private function constructOptionsMap(ProductVariantInterface $variant)
    {
        $optionMap = [];

        /** @var OptionValueInterface $option */
        foreach ($variant->getOptions() as $option) {
            $optionMap[$option->getOption()->getCode()] = $option->getValue();
        }

        $optionMap['value'] = $variant->getPrice();

        return $optionMap;
    }
}
