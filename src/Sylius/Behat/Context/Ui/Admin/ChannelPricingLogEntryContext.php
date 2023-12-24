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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\ChannelPricingLogEntry\IndexPageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ChannelPricingLogEntryContext implements Context
{
    public function __construct(private IndexPageInterface $indexPage)
    {
    }

    /**
     * @When /^I go to the price history of a (variant with code "[^"]+")$/
     */
    public function iGoToThePriceHistoryOfAVariant(ProductVariantInterface $productVariant): void
    {
        $channelPricing = $productVariant->getChannelPricings()->first();
        $product = $productVariant->getProduct();

        $this->indexPage->open([
            'productId' => $product->getId(),
            'variantId' => $productVariant->getId(),
            'channelPricingId' => $channelPricing->getId(),
        ]);
    }

    /**
     * @Then I should see :count log entries in the catalog price history
     * @Then I should see a single log entry in the catalog price history
     */
    public function iShouldSeeLogEntriesInTheCatalogPriceHistoryForTheVariant(int $count = 1): void
    {
        Assert::same($this->indexPage->countItems(), $count);
    }

    /**
     * @Then /^there should be a log entry on the (\d+)(?:|st|nd|rd|th) position with the "([^"]+)" selling price, "([^"]+)" original price and datetime of the price change$/
     * @Then /^there should be a log entry on the (\d+)(?:|st|nd|rd|th) position with the "([^"]+)" selling price, no original price and datetime of the price change$/
     */
    public function thereShouldBeALogEntryOnThePositionWithTheSellingPriceOriginalPriceAndDatetimeOfThePriceChange(
        int $position,
        string $price,
        string $originalPrice = '-',
    ): void {
        Assert::true($this->indexPage->isLogEntryWithPriceAndOriginalPriceOnPosition($price, $originalPrice, $position));
    }

    /**
     * @Then /^there should be a log entry with the "([^"]+)" selling price, "([^"]+)" original price and datetime of the price change$/
     * @Then /^there should be a log entry with the "([^"]+)" selling price, no original price and datetime of the price change$/
     */
    public function thereShouldBeALogEntryWithTheSellingPriceOriginalPriceAndDatetimeOfThePriceChange(
        string $price,
        string $originalPrice = '-',
    ): void {
        Assert::true($this->indexPage->isLogEntryWithPriceAndOriginalPrice($price, $originalPrice));
    }
}
