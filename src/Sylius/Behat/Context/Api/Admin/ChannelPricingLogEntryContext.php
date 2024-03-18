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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ChannelPricingLogEntryContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When /^I go to the price history of a (variant with code "[^"]+")$/
     */
    public function iGoToThePriceHistoryOfAVariant(ProductVariantInterface $productVariant): void
    {
        $channel = $this->sharedStorage->get('channel');
        Assert::notNull($channel);

        $this->sharedStorage->set('variant', $productVariant);

        $this->client->index(Resources::CHANNEL_PRICING_LOG_ENTRIES);
        $this->client->addFilter('channelPricing.channelCode', $channel->getCode());
        $this->client->addFilter('channelPricing.productVariant.code', $productVariant->getCode());
        $this->client->filter();
    }

    /**
     * @Then I should see :count log entries in the catalog price history
     * @Then I should see a single log entry in the catalog price history
     */
    public function iShouldSeeLogEntriesInTheCatalogPriceHistoryForTheVariant(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then /^there should be a log entry on the (\d+)(?:|st|nd|rd|th) position with the ("[^"]+") selling price, (no|"[^"]+") original price and datetime of the price change$/
     */
    public function thereShouldBeALogEntryOnThePositionWithTheSellingPriceOriginalPriceAndDatetimeOfThePriceChange(
        int $position,
        int $price,
        int|string $originalPrice,
    ): void {
        if ('no' === $originalPrice) {
            $originalPrice = null;
        }

        $logEntry = $this->responseChecker->getCollection($this->client->getLastResponse())[$position - 1];

        Assert::same($logEntry['price'], $price);
        Assert::same($logEntry['originalPrice'], $originalPrice);
        Assert::keyExists($logEntry, 'loggedAt');
    }

    /**
     * @Then /^there should be a log entry with the ("[^"]+") selling price, (no|"[^"]+") original price and datetime of the price change$/
     */
    public function thereShouldBeALogEntryWithTheSellingPriceOriginalPriceAndDatetimeOfThePriceChange(
        int $price,
        int|string $originalPrice,
    ): void {
        $this->thereShouldBeALogEntryOnThePositionWithTheSellingPriceOriginalPriceAndDatetimeOfThePriceChange(
            1,
            $price,
            $originalPrice,
        );
    }
}
