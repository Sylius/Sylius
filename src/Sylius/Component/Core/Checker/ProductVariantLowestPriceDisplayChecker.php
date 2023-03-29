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

namespace Sylius\Component\Core\Checker;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ProductVariantLowestPriceDisplayChecker implements ProductVariantLowestPriceDisplayCheckerInterface
{
    public function isLowestPriceDisplayable(ProductVariantInterface $productVariant, array $context): bool
    {
        Assert::keyExists($context, 'channel');
        $channel = $context['channel'];
        Assert::isInstanceOf($channel, ChannelInterface::class);

        if (!$channel->isLowestPriceForDiscountedProductsVisible()) {
            return false;
        }

        /** @var ProductInterface $product */
        $product = $productVariant->getProduct();
        $taxons = $product->getTaxons();
        if ($taxons->isEmpty()) {
            return true;
        }

        $excludedTaxons = $channel->getTaxonsExcludedFromShowingLowestPrice();
        if ($excludedTaxons->isEmpty()) {
            return true;
        }

        return !$this->isAnyTaxonExcluded($taxons->toArray(), $excludedTaxons->toArray());
    }

    private function isAnyTaxonExcluded(array $taxons, array $excludedTaxons): bool
    {
        if ($this->isCommonPart($taxons, $excludedTaxons)) {
            return true;
        }

        /** @var TaxonInterface $excludedTaxon */
        foreach ($excludedTaxons as $excludedTaxon) {
            $children = $excludedTaxon->getChildren();
            if (!$children->isEmpty()) {
                if ($this->isAnyTaxonExcluded($taxons, $children->toArray())) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isCommonPart(array $firstArray, array $secondArray): bool
    {
        return 0 < count(array_uintersect(
            $firstArray,
            $secondArray,
            /** @phpstan-ignore-next-line  */
            fn (TaxonInterface $firstTaxon, TaxonInterface $secondTaxon): int => $firstTaxon->getCode() <=> $secondTaxon->getCode(),
        ));
    }
}
