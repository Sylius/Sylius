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

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemoverInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ManagingPriceHistoryContext implements Context
{
    public function __construct(
        private RepositoryInterface $channelPricingLogEntryRepository,
        private ProductVariantResolverInterface $variantResolver,
        private ChannelPricingLogEntriesRemoverInterface $channelPricingLogEntriesRemover,
    ) {
    }

    /**
     * @When I delete price history older than :days day(s)
     */
    public function iDeletePriceHistoryOlderThanDays(int $days): void
    {
        $this->channelPricingLogEntriesRemover->remove($days);
    }

    /**
     * @Then /^there should be (\d+) price history entries for (this product)$/
     */
    public function thereShouldBeCountPriceHistoryEntriesForThisProduct(int $count, ProductInterface $product): void
    {
        $channelPricingLogEntries = $this->channelPricingLogEntryRepository->findBy([
            'channelPricing' => $this->getChannelPricingFromProduct($product),
        ]);

        Assert::count($channelPricingLogEntries, $count);
    }

    /**
     * @Then /^(this product) should have no entry with original price changed to ("[^"]+")$/
     */
    public function thisProductShouldHaveNoEntryWithOriginalPriceChangedTo(
        ProductInterface $product,
        int $originalPrice,
    ): void {
        Assert::null($this->channelPricingLogEntryRepository->findOneBy([
            'channelPricing' => $this->getChannelPricingFromProduct($product),
            'originalPrice' => $originalPrice,
        ]));
    }

    /**
     * @Then /^(this product)'s price history should be empty$/
     */
    public function thisProductsPriceHistoryShouldBeEmpty(ProductInterface $product): void
    {
        $this->thereShouldBeCountPriceHistoryEntriesForThisProduct(0, $product);
    }

    private function getChannelPricingFromProduct(ProductInterface $product): ChannelPricingInterface
    {
        $variant = $this->variantResolver->getVariant($product);
        Assert::notNull($variant);

        $channelPricing = $variant->getChannelPricings()->first();
        Assert::isInstanceOf($channelPricing, ChannelPricingInterface::class);

        return $channelPricing;
    }
}
