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

interface VariantsElementInterface
{
    public function countVariantsOnPage(): int;

    public function hasProductVariant(string $name): bool;

    public function hasProductVariantWithCodePriceAndCurrentStock(
        string $name,
        string $code,
        string $price,
        string $currentStock,
        string $channel,
    ): bool;

    public function hasProductVariantWithLowestPriceBeforeDiscountInChannel(
        string $productVariantName,
        string $lowestPriceBeforeDiscount,
        string $channelName,
    ): bool;
}
