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

interface PricingElementInterface
{
    public function getPriceForChannel(string $channelName): string;

    public function getOriginalPriceForChannel(string $channelName): string;

    public function getCatalogPromotionsNamesForChannel(string $channelName): array;

    public function getCatalogPromotionLinksForChannel(string $channelName): array;

    public function getLowestPriceBeforeDiscountForChannel(string $channelName): string;

    public function getSimpleProductPricingRowForChannel(string $channelName): NodeElement;

    public function getVariantPricingRowForChannel(string $variantName, string $channelName): NodeElement;
}
