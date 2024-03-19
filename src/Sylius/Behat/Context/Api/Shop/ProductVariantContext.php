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

namespace Sylius\Behat\Context\Api\Shop;

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Webmozart\Assert\Assert;

final class ProductVariantContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private IriConverterInterface $iriConverter,
    ) {
    }

    /**
     * @When I select :variant variant
     * @When I view :variant variant
     * @When I view :variant variant of the :product product
     */
    public function iSelectVariant(ProductVariantInterface $variant): void
    {
        $this->sharedStorage->set('variant', $variant);
        $this->client->show(Resources::PRODUCT_VARIANTS, $variant->getCode());
    }

    /**
     * @When the visitor view :variant variant
     */
    public function visitorViewVariant(ProductVariantInterface $variant): void
    {
        $this->sharedStorage->set('token', null);
        $this->client->show(Resources::PRODUCT_VARIANTS, $variant->getCode());
    }

    /**
     * @When I view variants
     */
    public function iViewVariants(): void
    {
        $response = $this->client->index(Resources::PRODUCT_VARIANTS);

        $this->sharedStorage->set('response', $response);
    }

    /**
     * @When /^I view variants of the ("[^"]+" product)$/
     */
    public function iViewVariantsOfTheProduct(ProductInterface $product): void
    {
        $response = $this->client->index(Resources::PRODUCT_VARIANTS, ['product' => $this->iriConverter->getIriFromResource($product)]);

        $this->sharedStorage->set('product_variant_collection', $this->responseChecker->getCollection($response));
    }

    /**
     * @When /^I filter (?:them|variants) by ("[^"]+" option value)$/
     */
    public function iFilterVariantsByOption(ProductOptionValueInterface $optionValue): void
    {
        $this->client->addFilter('optionValues[]', $this->iriConverter->getIriFromResource($optionValue));
        $response = $this->client->filter();

        $this->sharedStorage->set('product_variant_collection', $this->responseChecker->getCollection($response));
    }

    /**
     * @Then /^(?:the|this) product variant price should be ("[^"]+")$/
     * @Then /^I should see the variant price ("[^"]+")$/
     */
    public function theProductVariantPriceShouldBe(int $price): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        Assert::same($response['price'], $price);
    }

    /**
     * @Then /^(?:the|this) product original price should be ("[^"]+")$/
     */
    public function theProductOriginalPriceShouldBe(int $originalPrice): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

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
        string ...$promotionsNames,
    ): void {
        $content = $this->findVariant($variant);

        Assert::same($content['price'], $price);
        Assert::same($content['originalPrice'], $originalPrice);
        foreach ($content['appliedPromotions'] as $promotionIri) {
            $catalogPromotionContent = $this->responseChecker->getResponseContent(
                $this->client->showByIri($promotionIri),
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
        int $numberOfPromotions,
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
        string $promotionName,
    ): void {
        $variantContent = $this->findVariant($variant);
        $catalogPromotionResponse = $this->client->showByIri($variantContent['appliedPromotions'][0]);
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
        string $promotionName,
    ): void {
        $this->sharedStorage->set('token', null);
        $this->client->show(Resources::PRODUCT_VARIANTS, $productVariant->getCode());

        $this->iShouldSeeVariantIsDiscountedFromToWithPromotions($productVariant, $originalPrice, $price, $promotionName);
    }

    /**
     * @Then /^the visitor should(?:| still) see that the ("[^"]+" variant) is discounted from ("[^"]+") to ("[^"]+") with ([^"]+) promotions$/
     */
    public function theVisitorShouldSeeVariantIsDiscountedFromToWithNumberOfPromotions(
        ProductVariantInterface $variant,
        int $originalPrice,
        int $price,
        int $numberOfPromotions,
    ): void {
        $this->sharedStorage->set('token', null);
        $this->client->show(Resources::PRODUCT_VARIANTS, $variant->getCode());

        $this->iShouldSeeVariantIsDiscountedFromToWithNumberOfPromotions($variant, $originalPrice, $price, $numberOfPromotions);
    }

    /**
     * @Then /^I should see ("[^"]+" variant) is not discounted$/
     */
    public function iShouldSeeVariantIsNotDiscounted(ProductVariantInterface $variant): void
    {
        $response = $this->sharedStorage->has('response') ? $this->sharedStorage->get('response') : $this->client->getLastResponse();

        $items = $this->responseChecker->getCollectionItemsWithValue($response, 'code', $variant->getCode());
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
        $content = $this->responseChecker->getResponseContent($this->client->show(Resources::PRODUCT_VARIANTS, $variant->getCode()));

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
            $content = $this->responseChecker->getResponseContent($this->client->show(Resources::PRODUCT_VARIANTS, $variant->getCode()));
            Assert::keyExists(
                $content,
                'appliedPromotions',
                sprintf('%s variant should be discounted', $variant->getName()),
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
            $content = $this->responseChecker->getResponseContent($this->client->show(Resources::PRODUCT_VARIANTS, $variant->getCode()));
            Assert::keyNotExists(
                $content,
                'appliedPromotions',
                sprintf('%s variant should not be discounted', $variant->getName()),
            );
        }
    }

    /**
     * @Then I should not see :variant variant
     */
    public function iShouldNotSeeVariant(ProductVariantInterface $variant): void
    {
        $response = $this->client->show(Resources::PRODUCT_VARIANTS, $variant->getCode());

        Assert::same(
            $response->getStatusCode(),
            404,
            sprintf('%s variant should be disabled', $variant->getName()),
        );
    }

    /**
     * @Then /^I should see ("([^"]+)", "([^"]+)" and "([^"]+)" variants)$/
     */
    public function variantAndVariantShouldBeVisible(array $variants): void
    {
        $this->sharedStorage->set('token', null);

        /** @var ProductVariantInterface $variant */
        foreach ($variants as $variant) {
            $content = $this->responseChecker->getResponseContent($this->client->show(Resources::PRODUCT_VARIANTS, $variant->getCode()));
            Assert::same(
                $content['name'],
                $variant->getName(),
                sprintf('%s variant should be visible', $variant->getName()),
            );
        }
    }

    /**
     * @Then /^I should see variant with ("[^"]+" option) and ("[^"]+" option value) priced at ("[^"]+") at (\d)(?:st|nd|rd|th) position$/
     */
    public function iShouldSeeVariantWithOptionPricedAtAtPosition(
        string $expectedOptionName,
        string $expectedOptionValueValue,
        int $price,
        int $position,
    ): void {
        $variants = $this->sharedStorage->get('product_variant_collection');
        Assert::greaterThan(count($variants), $position - 1, 'There are less variants than expected');

        $variant = $variants[$position - 1];
        Assert::same($variant['price'], $price);

        foreach ($variant['optionValues'] as $optionValue) {
            $optionValueData = $this->fetchItemByIri($optionValue);
            $optionData = $this->fetchItemByIri($optionValueData['option']);

            if ($optionData['name'] === $expectedOptionName && $optionValueData['value'] === $expectedOptionValueValue) {
                return;
            }
        }

        throw new \InvalidArgumentException(sprintf(
            'There is no variant with "%s" option and "%s" option value',
            $expectedOptionName,
            $expectedOptionValueValue,
        ));
    }

    /**
     * @Then /^I should not see variant with "([^"]+)" option "([^"]+)"$/
     */
    public function iShouldNotSeeVariantWithOptionPricedAt(string $expectedOptionName, string $expectedOptionValueValue): void
    {
        $variants = $this->sharedStorage->get('product_variant_collection');

        foreach ($variants as $variant) {
            foreach ($variant['optionValues'] as $optionValueIri) {
                $optionValueData = $this->fetchItemByIri($optionValueIri);
                $optionData = $this->fetchItemByIri($optionValueData['option']);

                Assert::false(
                    $optionData['name'] === $expectedOptionName &&
                    $optionValueData['value'] === $expectedOptionValueValue,
                );
            }
        }
    }

    /**
     * @Then I should not see any variants
     */
    public function iShouldNotSeeAnyVariants(): void
    {
        Assert::same(
            count($this->sharedStorage->get('product_variant_collection')),
            0,
        );
    }

    private function findVariant(?ProductVariantInterface $variant): array
    {
        $response = $this->sharedStorage->has('response') ? $this->sharedStorage->get('response') : $this->client->getLastResponse();

        if ($variant !== null && $this->responseChecker->hasValue($response, '@type', 'hydra:Collection')) {
            $returnValue = $this->responseChecker->getCollectionItemsWithValue($response, 'code', $variant->getCode());

            return array_shift($returnValue);
        }

        return $this->responseChecker->getResponseContent($response);
    }

    private function fetchItemByIri(string $iri): array
    {
        return $this->responseChecker->getResponseContent($this->client->showByIri($iri));
    }
}
