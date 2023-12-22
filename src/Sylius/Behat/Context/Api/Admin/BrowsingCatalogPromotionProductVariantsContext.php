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

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class BrowsingCatalogPromotionProductVariantsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
    ) {
    }

    /**
     * @When I browse variants affected by catalog promotion :catalogPromotion
     */
    public function iBrowseVariantsAffectedByCatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->client->index(Resources::PRODUCT_VARIANTS);
        $this->client->addFilter('catalogPromotion', $this->iriConverter->getIriFromResource($catalogPromotion));
        $this->client->filter();
    }

    /**
     * @When /^I want to view all variants of (this product)$/
     * @When /^I view(?:| all) variants of the (product "[^"]+")$/
     */
    public function iWantToViewAllVariantsOfThisProduct(ProductInterface $product): void
    {
        $this->client->index(Resources::PRODUCT_VARIANTS);
        $this->client->addFilter('product', $this->iriConverter->getIriFromResource($product));
        $this->client->filter();
    }

    /**
     * @Then /^there should be (\d+) product variants? on the list$/
     */
    public function thereShouldBeProductVariantsOnTheList(int $count): void
    {
        Assert::same(
            $this->responseChecker->countCollectionItems($this->client->getLastResponse()),
            $count,
        );
    }

    /**
     * @Then it should be the :variantName product variant
     * @Then it should be :firstVariant and :secondVariant product variants
     */
    public function theProductVariantShouldBeInTheRegistry(string ...$variantsNames): void
    {
        foreach ($variantsNames as $variantName) {
            Assert::true($this->responseChecker->hasItemWithTranslation(
                $this->client->getLastResponse(),
                'en_US',
                'name',
                $variantName,
            ));
        }
    }

    /**
     * @Then :variant variant price should be decreased by catalog promotion :catalogPromotion in :channel channel
     */
    public function variantPriceShouldBeDecreasedByCatalogPromotion(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel,
    ): void {
        Assert::true(
            $this->variantHasCatalogPromotionInChannel($variant, $catalogPromotion, $channel),
            sprintf(
                'Catalog promotion "%s" was not found in applied promotions of variant "%s" in channel "%s".',
                $catalogPromotion->getCode(),
                $variant->getCode(),
                $channel->getCode(),
            ),
        );
    }

    /**
     * @Then :variant variant price should not be decreased by catalog promotion :catalogPromotion in :channel channel
     */
    public function variantPriceShouldNotBeDecreasedByCatalogPromotion(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel,
    ): void {
        Assert::false(
            $this->variantHasCatalogPromotionInChannel($variant, $catalogPromotion, $channel),
            sprintf(
                'Catalog promotion "%s" was found in applied promotions of variant "%s" in channel "%s".',
                $catalogPromotion->getCode(),
                $variant->getCode(),
                $channel->getCode(),
            ),
        );
    }

    private function variantHasCatalogPromotionInChannel(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel,
    ): bool {
        $variantData = $this->getDataOfVariantWithCode($variant->getCode());

        $promotions = $variantData['channelPricings'][$channel->getCode()]['appliedPromotions'] ?? [];
        foreach ($promotions as $promotion) {
            if ($promotion['code'] === $catalogPromotion->getCode()) {
                return true;
            }
        }

        return false;
    }

    private function getDataOfVariantWithCode(string $code): array
    {
        $variantsData = $this->responseChecker->getCollection($this->client->getLastResponse());
        foreach ($variantsData as $variantData) {
            if ($variantData['code'] === $code) {
                return $variantData;
            }
        }

        throw new \InvalidArgumentException(sprintf('Variant with code "%s" was not found.', $code));
    }
}
