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
    public function getPriceForChannel(string $channelCode): string;

    public function getOriginalPriceForChannel(string $channelCode): string;

    public function getCatalogPromotionsNamesForChannel(string $channelCode): array;

    public function getCatalogPromotionLinksForChannel(string $channelCode): array;

    public function getLowestPriceBeforeDiscountForChannel(string $channelCode): string;

    public function getSimpleProductPricingRowForChannel(string $channelCode): NodeElement;

    public function getVariantPricingRowForChannel(string $variantCode, string $channelCode): NodeElement;
}
