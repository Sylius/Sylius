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

namespace Sylius\Component\Core\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class ProductVariantsPricesProvider implements ProductVariantsPricesProviderInterface
{
    public function __construct(private ProductVariantPriceCalculatorInterface $productVariantPriceCalculator)
    {
    }

    public function provideVariantsPrices(ProductInterface $product, ChannelInterface $channel): array
    {
        $variantsPrices = [];

        /** @var ProductVariantInterface $variant */
        foreach ($product->getEnabledVariants() as $variant) {
            $variantsPrices[] = $this->constructOptionsMap($variant, $channel);
        }

        return $variantsPrices;
    }

    private function constructOptionsMap(ProductVariantInterface $variant, ChannelInterface $channel): array
    {
        $optionMap = [];

        /** @var ProductOptionValueInterface $option */
        foreach ($variant->getOptionValues() as $option) {
            /** @var string $optionCode */
            $optionCode = $option->getOptionCode();
            $optionMap[$optionCode] = $option->getCode();
        }

        $price = $this->productVariantPriceCalculator->calculate($variant, ['channel' => $channel]);
        $optionMap['value'] = $price;

        /** @var ArrayCollection $appliedPromotions */
        $appliedPromotions = $variant->getAppliedPromotionsForChannel($channel);
        if (!$appliedPromotions->isEmpty()) {
            $optionMap['applied_promotions'] = $appliedPromotions->toArray();
        }

        if (!$this->productVariantPriceCalculator instanceof ProductVariantPricesCalculatorInterface) {
            return $optionMap;
        }

        $lowestPriceBeforeDiscount = $this->productVariantPriceCalculator->calculateLowestPriceBeforeDiscount($variant, ['channel' => $channel]);

        if ($lowestPriceBeforeDiscount !== null) {
            $optionMap['lowest-price-before-discount'] = $lowestPriceBeforeDiscount;
        }

        $originalPrice = $this->productVariantPriceCalculator->calculateOriginal($variant, ['channel' => $channel]);

        if ($originalPrice > $price) {
            $optionMap['original-price'] = $originalPrice;
        }

        return $optionMap;
    }
}
