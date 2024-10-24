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

namespace Sylius\Behat\Element\Product\ShowPage;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Element\Element;

final class PricingElement extends Element implements PricingElementInterface
{
    public function getPriceForChannel(string $channelCode): string
    {
        $channelPriceRow = $this->getChannelPriceRow($channelCode);

        if (null === $channelPriceRow) {
            return '';
        }

        $priceForChannel = $channelPriceRow->find('css', '[data-test-price]');

        return $priceForChannel->getText();
    }

    public function getOriginalPriceForChannel(string $channelCode): string
    {
        $channelPriceRow = $this->getChannelPriceRow($channelCode);

        $priceForChannel = $channelPriceRow->find('css', '[data-test-original-price]');

        return $priceForChannel->getText();
    }

    public function getCatalogPromotionsNamesForChannel(string $channelCode): array
    {
        /** @var NodeElement[] $appliedPromotions */
        $appliedPromotions = $this->getAppliedPromotionsForChannel($channelCode);

        return array_map(fn (NodeElement $element): string => $element->getText(), $appliedPromotions);
    }

    public function getCatalogPromotionLinksForChannel(string $channelCode): array
    {
        $appliedPromotions = $this->getAppliedPromotionsForChannel($channelCode);

        return array_map(fn (NodeElement $element): string => $element->getAttribute('href'), $appliedPromotions);
    }

    public function getLowestPriceBeforeDiscountForChannel(string $channelCode): string
    {
        $channelPriceRow = $this->getSimpleProductPricingRowForChannel($channelCode);

        if (null === $channelPriceRow) {
            throw new \InvalidArgumentException(sprintf('Channel "%s" does not exist', $channelCode));
        }

        $priceForChannel = $channelPriceRow->find('css', 'td:nth-child(4)');

        return $priceForChannel->getText();
    }

    public function getSimpleProductPricingRowForChannel(string $channelCode): NodeElement
    {
        return $this->getElement('simple_product_pricing_row', ['%channel_code%' => $channelCode]);
    }

    public function getVariantPricingRowForChannel(string $variantCode, string $channelCode): NodeElement
    {
        return $this->getElement('variant_pricing_row', [
            '%variant_code%' => $variantCode,
            '%channel_code%' => $channelCode,
        ]);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'price_row' => '[data-test-pricing="%channel_code%"]',
            'simple_product_pricing_row' => '[data-test-simple-product="%channel_code%"]',
            'variant_pricing_row' => '[data-test-variant-pricing="%channel_code%.%variant_code%"]',
        ]);
    }

    private function getAppliedPromotionsForChannel(string $channelCode): array
    {
        /** @var NodeElement $channelPriceRow */
        $channelPriceRow = $this->getChannelPriceRow($channelCode);

        return $channelPriceRow->findAll('css', '[data-test-applied-promotion]');
    }

    private function getChannelPriceRow(string $channelCode): ?NodeElement
    {
        try {
            return $this->getElement('price_row', ['%channel_code%' => $channelCode]);
        } catch (ElementNotFoundException) {
            return null;
        }
    }
}
