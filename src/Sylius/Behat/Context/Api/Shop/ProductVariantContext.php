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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ProductVariantContext implements Context
{
    private ApiClientInterface $variantClient;

    private ApiClientInterface $catalogPromotionClient;

    private ResponseCheckerInterface $responseChecker;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        ApiClientInterface $variantClient,
        ApiClientInterface $catalogPromotionClient,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->variantClient = $variantClient;
        $this->catalogPromotionClient = $catalogPromotionClient;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I select :variant variant
     * @When I view :variant variant
     * @When I view :variant variant of the :product product
     */
    public function iSelectVariant(ProductVariantInterface $variant): void
    {
        $this->sharedStorage->set('variant', $variant);
        $this->variantClient->show($variant->getCode());
    }

    /**
     * @When the visitor view :variant variant
     */
    public function visitorViewVariant(ProductVariantInterface $variant): void
    {
        $this->sharedStorage->set('token', null);
        $this->variantClient->show($variant->getCode());
    }

    /**
     * @When I view variants
     */
    public function iViewVariants(): void
    {
        $this->variantClient->index();
    }

    /**
     * @Then /^(?:the|this) product variant price should be ("[^"]+")$/
     * @Then /^I should see the variant price ("[^"]+")$/
     */
    public function theProductVariantPriceShouldBe(int $price): void
    {
        $response = $this->responseChecker->getResponseContent($this->variantClient->getLastResponse());

        Assert::same($response['price'], $price);
    }

    /**
     * @Then /^(?:the|this) product original price should be ("[^"]+")$/
     */
    public function theProductOriginalPriceShouldBe(int $originalPrice): void
    {
        $response = $this->responseChecker->getResponseContent($this->variantClient->getLastResponse());

        Assert::same($response['originalPrice'], $originalPrice);
    }

    /**
     * @Then /^I should see ("[^"]+" variant) is discounted from ("[^"]+") to ("[^"]+") with "([^"]+)" promotion$/
     * @Then /^I should see (this variant) is discounted from ("[^"]+") to ("[^"]+") with "([^"]+)" promotion$/
     * @Then /^I should see (this variant) is discounted from ("[^"]+") to ("[^"]+") with "([^"]+)" and "([^"]+)" promotions$/
     * @Then /^I should see (this variant) is discounted from ("[^"]+") to ("[^"]+") with "([^"]+)", "([^"]+)" and "([^"]+)" promotions$/
     * @Then /^I should see (this variant) is discounted from ("[^"]+") to ("[^"]+") with "([^"]+)", "([^"]+)", "([^"]+)" and "([^"]+)" promotions$/
     */
    public function iShouldSeeVariantIsDiscountedFromToWithPromotions(
        ProductVariantInterface $variant,
        int $originalPrice,
        int $price,
        string ...$promotionsNames
    ): void {
        $content = $this->findVariant($variant);

        Assert::same($content['price'], $price);
        Assert::same($content['originalPrice'], $originalPrice);
        foreach ($content['appliedPromotions'] as $promotionIri) {
            $catalogPromotionContent = $this->responseChecker->getResponseContent(
                $this->catalogPromotionClient->showByIri($promotionIri)
            );
            Assert::inArray($catalogPromotionContent['label'], $promotionsNames);
        }
    }

    /**
     * @Then /^I should see (this variant) is discounted from ("[^"]+") to ("[^"]+") with ([^"]+) promotions$/
     */
    public function iShouldSeeVariantIsDiscountedFromToWithNumberOfPromotions(
        ProductVariantInterface $variant,
        int $originalPrice,
        int $price,
        int $numberOfPromotions
    ): void {
        $content = $this->findVariant($variant);

        Assert::same($content['price'], $price);
        Assert::same($content['originalPrice'], $originalPrice);
        Assert::count($content['appliedPromotions'], $numberOfPromotions);
    }

    /**
     * @Then /^I should see (this variant) is discounted from ("[^"]+") to ("[^"]+") with only "([^"]+)" promotion$/
     */
    public function iShouldSeeVariantIsDiscountedFromToWithOnlyPromotion(
        ProductVariantInterface $variant,
        int $originalPrice,
        int $price,
        string $promotionName
    ): void {
        $variantContent = $this->findVariant($variant);
        $catalogPromotionResponse = $this->variantClient->showByIri($variantContent['appliedPromotions'][0]);
        $catalogPromotionContent = $this->responseChecker->getResponseContent($catalogPromotionResponse);

        Assert::count($variantContent['appliedPromotions'], 1);
        Assert::same($variantContent['price'], $price);
        Assert::same($variantContent['originalPrice'], $originalPrice);
        Assert::same($catalogPromotionContent['label'], $promotionName);
    }

    /**
     * @Then /^the visitor should(?:| still) see that the ("[^"]+" variant) is discounted from ("[^"]+") to ("[^"]+") with "([^"]+)" promotion$/
     */
    public function theVisitorShouldSeeThatTheVariantIsDiscountedWithPromotion(
        ProductVariantInterface $productVariant,
        int $originalPrice,
        int $price,
        string $promotionName
    ): void {
        $this->sharedStorage->set('token', null);
        $this->variantClient->show($productVariant->getCode());

        $this->iShouldSeeVariantIsDiscountedFromToWithPromotions($productVariant, $originalPrice, $price, $promotionName);
    }

    /**
     * @Then /^I should see ("[^"]+" variant) is not discounted$/
     */
    public function iShouldSeeVariantIsNotDiscounted(ProductVariantInterface $variant): void
    {
        $items = $this->responseChecker->getCollectionItemsWithValue($this->variantClient->getLastResponse(), 'code', $variant->getCode());
        $item = array_pop($items);
        Assert::keyNotExists($item, 'appliedPromotions');
    }

    /**
     * @Then /^the visitor should see (this variant) is not discounted$/
     * @Then /^the visitor should see that the ("[^"]+" variant) is not discounted$/
     */
    public function theVisitorShouldSeeThatTheVariantIsNotDiscounted(ProductVariantInterface $variant): void
    {
        $this->sharedStorage->set('token', null);

        $this->iShouldSeeThisVariantIsNotDiscounted($variant);
    }

    /**
     * @Then /^I should see (this variant) is not discounted$/
     */
    public function iShouldSeeThisVariantIsNotDiscounted(ProductVariantInterface $variant): void
    {
        $content = $this->responseChecker->getResponseContent($this->variantClient->show($variant->getCode()));

        Assert::keyNotExists($content, 'appliedPromotions');
    }

    /**
     * @Then /^("[^"]+" variant) and ("[^"]+" variant) should be discounted$/
     * @Then /^("[^"]+" variant) should be discounted$/
     */
    public function variantAndVariantShouldBeDiscounted(ProductVariantInterface ...$variants): void
    {
        $this->sharedStorage->set('token', null);

        /** @var ProductVariantInterface $variant */
        foreach ($variants as $variant) {
            $content = $this->responseChecker->getResponseContent($this->variantClient->show($variant->getCode()));
            Assert::keyExists(
                $content,
                'appliedPromotions',
                sprintf('%s variant should be discounted', $variant->getName())
            );
        }
    }

    /**
     * @Then /^("[^"]+" variant) and ("[^"]+" variant) should not be discounted$/
     * @Then /^("[^"]+" variant) should not be discounted$/
     */
    public function variantAndVariantShouldNotBeDiscounted(ProductVariantInterface ...$variants): void
    {
        $this->sharedStorage->set('token', null);

        /** @var ProductVariantInterface $variant */
        foreach ($variants as $variant) {
            $content = $this->responseChecker->getResponseContent($this->variantClient->show($variant->getCode()));
            Assert::keyNotExists(
                $content,
                'appliedPromotions',
                sprintf('%s variant should not be discounted', $variant->getName())
            );
        }
    }

    private function findVariant(?ProductVariantInterface $variant): array
    {
        $response = $this->variantClient->getLastResponse();

        if ($variant !== null && $this->responseChecker->hasValue($response, '@type', 'hydra:Collection')) {
            $returnValue = $this->responseChecker->getCollectionItemsWithValue($response, 'code', $variant->getCode());

            return array_shift($returnValue);
        }

        return $this->responseChecker->getResponseContent($response);
    }
}
