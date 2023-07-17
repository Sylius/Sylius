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

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Setter\ChannelContextSetterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private IriConverterInterface $iriConverter,
        private ChannelContextSetterInterface $channelContextSetter,
        private RequestFactoryInterface $requestFactory,
        private string $apiUrlPrefix,
    ) {
    }

    /**
     * @When /^I check (this product)'s details$/
     * @When I view product :product
     * @When customer view product :product
     */
    public function iViewProduct(ProductInterface $product): void
    {
        $this->client->show(Resources::PRODUCTS, $product->getCode());

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $product->getVariants()->first();

        $this->sharedStorage->set('product', $product);
        $this->sharedStorage->set('product_variant', $productVariant);
        $this->sharedStorage->remove('product_attributes');
    }

    /**
     * @When I view product :product in the :localeCode locale
     * @When /^I check (this product)'s details in the ("([^"]+)" locale)$/
     * @When /^I try to check (this product)'s details in the ("([^"]+)" locale)$/
     */
    public function iViewProductInTheLocale(ProductInterface $product, string $localeCode): void
    {
        $this->sharedStorage->set('current_locale_code', $localeCode);

        $this->iViewProduct($product);

        $this->sharedStorage->remove('current_locale_code');
    }

    /**
     * @When I view product :product using slug
     */
    public function iViewProductUsingSlug(ProductInterface $product): void
    {
        $this->client->showByIri(sprintf('%s/shop/products-by-slug/%s', $this->apiUrlPrefix, $product->getSlug()));

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @Then I should be redirected to :product product
     */
    public function iShouldBeRedirectedToProduct(ProductInterface $product): void
    {
        $response = $this->client->getLastResponse();

        Assert::eq($response->headers->get('Location'), sprintf('%s/shop/products/%s', $this->apiUrlPrefix, $product->getCode()));
    }

    /**
     * @When I browse products from taxon :taxon
     * @When I browse products
     */
    public function iBrowseProductsFromTaxon(?TaxonInterface $taxon = null): void
    {
        $this->client->index(Resources::PRODUCTS);

        if ($taxon !== null) {
            $this->client->addFilter('taxon', $this->iriConverter->getIriFromItem($taxon));
            $this->client->filter();
        }
    }

    /**
     * @When I browse products from product taxon code :taxon
     */
    public function iBrowseProductsFromProductTaxonCode(TaxonInterface $taxon): void
    {
        $this->client->index(Resources::PRODUCTS);
        $this->client->addFilter('productTaxons.taxon.code', $taxon->getCode());
        $this->client->filter();
    }

    /**
     * @When /^I browse products from ("([^"]+)" and "([^"]+)" taxons)$/
     */
    public function iBrowseProductsFromProductTaxonCodes(iterable $taxons): void
    {
        $this->client->index(Resources::PRODUCTS);

        foreach ($taxons as $index => $taxon) {
            $this->client->addFilter('productTaxons.taxon.code[' . $index . ']', $taxon->getCode());
        }

        $this->client->filter();
    }

    /**
     * @When I browse products from non existing taxon
     */
    public function iBrowseProductsFromNonExistingTaxon(): void
    {
        $this->client->index(Resources::PRODUCTS);

        $this->client->addFilter('taxon', 'non-existing-taxon');
        $this->client->filter();
    }

    /**
     * @When /^I sort products by the (oldest|newest) date first$/
     */
    public function iSortProductsByTheDateFirst(string $sortDirection): void
    {
        $sortDirection = 'oldest' === $sortDirection ? 'asc' : 'desc';

        $this->client->sort(['createdAt' => $sortDirection]);
    }

    /**
     * @When I sort products by the lowest price first
     */
    public function iSortProductsByTheLowestPriceFirst(): void
    {
        $this->client->sort(['price' => 'asc']);
    }

    /**
     * @When I sort products by the highest price first
     */
    public function iSortProductsByTheHighestPriceFirst(): void
    {
        $this->client->sort(['price' => 'desc']);
    }

    /**
     * @When I sort products alphabetically from a to z
     */
    public function iSortProductsAlphabeticallyFromAToZ(): void
    {
        $this->client->sort(['translation.name' => 'asc']);
    }

    /**
     * @When I sort products alphabetically from z to a
     */
    public function iSortProductsAlphabeticallyFromZToA(): void
    {
        $this->client->sort(['translation.name' => 'desc']);
    }

    /**
     * @When I clear filter
     */
    public function iClearFilter(): void
    {
        $this->client->clearParameters();
        $this->client->filter();
    }

    /**
     * @When I search for products with name :name
     */
    public function iSearchForProductsWithName(string $name): void
    {
        $this->client->addFilter('translations.name', $name);
        $this->client->filter();
    }

    /**
     * @Then I should see :rating as its average rating
     */
    public function iShouldSeeAsItsAverageRating(float $rating): void
    {
        Assert::same(round($this->responseChecker->getValue($this->client->getLastResponse(), 'averageRating'), 2), $rating);
    }

    /**
     * @Then I should see the product :name
     */
    public function iShouldSeeTheProduct(string $name): void
    {
        Assert::true($this->hasProductWithName(
            $this->responseChecker->getCollection($this->client->getLastResponse()),
            $name,
        ));
    }

    /**
     * @Then I should see a product with code :code
     */
    public function iShouldSeeAProductWithCode(string $code): void
    {
        Assert::true($this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'code', $code));
    }

    /**
     * @Then I should see a product with name :name
     */
    public function iShouldSeeAProductWithName(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'name', $name),
        );
    }

    /**
     * @Then I should see that it is out of stock
     */
    public function iShouldSeeItIsOutOfStock(): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->sharedStorage->get('product_variant');

        $variantResponse = $this->client->showByIri($this->iriConverter->getIriFromItem($productVariant));

        Assert::false($this->responseChecker->getValue($variantResponse, 'inStock'));
    }

    /**
     * @Then I should not see the product :name
     */
    public function iShouldNotSeeTheProduct(string $name): void
    {
        Assert::false($this->hasProductWithName(
            $this->responseChecker->getCollection($this->client->getLastResponse()),
            $name,
        ));
    }

    /**
     * @Then /^I should see the product price ("[^"]+")$/
     * @Then /^customer should see the product price ("[^"]+")$/
     */
    public function iShouldSeeTheProductPrice(int $price): void
    {
        Assert::true($this->hasProductWithPrice(
            [$this->responseChecker->getResponseContent($this->client->getLastResponse())],
            $price,
        ));
    }

    /**
     * @Then /^I should see the product original price ("[^"]+")$/
     * @Then /^customer should see the product original price ("[^"]+")$/
     */
    public function iShouldSeeTheProductOriginalPrice(int $originalPrice): void
    {
        /** @var ProductVariantInterface $checkedVariant */
        $checkedVariant = $this->sharedStorage->get('product_variant');
        $variant = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        Assert::same($variant['originalPrice'], $originalPrice);
        Assert::same($variant['code'], $checkedVariant->getCode());
    }

    /**
     * @Then I should see this product has no catalog promotion applied
     */
    public function iShouldSeeThisProductHasNoCatalogPromotionApplied(): void
    {
        $variant = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        Assert::same($variant['originalPrice'], $variant['price']);
        Assert::keyNotExists($variant, 'appliedPromotions');
    }

    /**
     * @Then I should not see any original price
     */
    public function iShouldNotSeeAnyOriginalPrice(): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        Assert::same($response['originalPrice'], $response['price']);
    }

    /**
     * @Then /^I should see ("[^"]+" product) discounted from ("[^"]+") to ("[^"]+")$/
     */
    public function iShouldSeeProductDiscountedFromTo(ProductInterface $product, int $originalPrice, int $price): void
    {
        $lastResponse = $this->client->getLastResponse();

        $this->iShouldSeeTheProductWithPrice($product, $price);
        Assert::true(
            $this->hasProductWithPrice(
                $this->responseChecker->getCollection($lastResponse),
                $originalPrice,
                $product->getCode(),
                'originalPrice',
            ),
            sprintf('There is no product with %s code and %s original price', $product->getCode(), $originalPrice),
        );
    }

    /**
     * @Then /^I should see the (product "[^"]+") with price ("[^"]+")$/
     */
    public function iShouldSeeTheProductWithPrice(ProductInterface $product, int $price): void
    {
        Assert::true(
            $this->hasProductWithPrice(
                $this->responseChecker->getCollection($this->client->getLastResponse()),
                $price,
                $product->getCode(),
            ),
            sprintf('There is no product with %s code and %s price', $product->getCode(), $price),
        );
    }

    /**
     * @Then I should see the product :product with short description :shortDescription
     */
    public function iShouldSeeTheProductWithShortDescription(ProductInterface $product, string $shortDescription): void
    {
        Assert::true(
            $this->hasProductWithNameAndShortDescription(
                $this->responseChecker->getCollection($this->client->getLastResponse()),
                $product->getName(),
                $shortDescription,
            ),
            sprintf('There is no product with %s name and %s short description', $product->getName(), $shortDescription),
        );
    }

    /**
     * @Then the first product on the list should have code :code
     */
    public function theFirstProductOnTheListShouldHaveCode(string $code): void
    {
        $products = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same($products[0]['code'], $code);
    }

    /**
     * @Then the last product on the list should have code :value
     */
    public function theLastProductOnTheListShouldHaveCode(string $code): void
    {
        $products = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same(end($products)['code'], $code);
    }

    /**
     * @Then the first product on the list should have name :name
     */
    public function theFirstProductOnTheListShouldHaveName(string $name): void
    {
        $products = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same($products[0]['name'], $name);
    }

    /**
     * @Then /^the first product on the list should have name "([^"]+)" and price ("[^"]+")$/
     */
    public function theFirstProductOnTheListShouldHaveNameAndPrice(string $name, int $price): void
    {
        $product = $this->responseChecker->getCollection($this->client->resend())[0];

        $defaultVariantPrice = $this->responseChecker->getValue(
            $this->client->showByIri($product['defaultVariant']),
            'price',
        );

        Assert::same($product['name'], $name);
        Assert::same($defaultVariantPrice, $price);
    }

    /**
     * @Then the last product on the list should have name :name
     */
    public function theLastProductOnTheListShouldHaveName(string $name): void
    {
        $products = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same(end($products)['name'], $name);
    }

    /**
     * @Then /^the last product on the list should have name "([^"]+)" and price ("[^"]+")$/
     */
    public function theLastProductOnTheListShouldHaveNameAndPrice(string $name, int $price): void
    {
        $products = $this->responseChecker->getCollection($this->client->resend());
        $product = end($products);

        $defaultVariantPrice = $this->responseChecker->getValue(
            $this->client->showByIri($product['defaultVariant']),
            'price',
        );

        Assert::same($product['name'], $name);
        Assert::same($defaultVariantPrice, $price);
    }

    /**
     * @When /^I should see only (\d+) product(s)$/
     */
    public function iShouldSeeOnlyProducts(int $count): void
    {
        Assert::same(
            count($this->responseChecker->getCollection($this->client->getLastResponse())),
            $count,
            'Number of products from response is different then expected',
        );
    }

    /**
     * @Then I should not see the product with name :name
     */
    public function iShouldNotSeeProductWithName(string $name): void
    {
        Assert::false($this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'name', $name));
    }

    /**
     * @Then I should see the product name :name
     */
    public function iShouldSeeProductName(string $name): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'name', $name));
    }

    /**
     * @Then /^I should not be able to view (this product) in the ("([^"]+)" locale)$/
     */
    public function iShouldNotBeAbleToViewThisProductInLocale(ProductInterface $product, string $localeCode): void
    {
        Assert::false($this->responseChecker->hasValue(
            $this->client->getLastResponse(),
            'name',
            $product->getTranslation($localeCode)->getName(),
        ));
    }

    /**
     * @Then its current variant should be named :variantName
     */
    public function itsCurrentVariantShouldBeNamed(string $variantName): void
    {
        $response = $this->client->getLastResponse();

        $productVariant = $this->responseChecker->getValue($response, 'variants');
        $request = $this->requestFactory->custom($productVariant[0], HttpRequest::METHOD_GET);
        $this->client->executeCustomRequest($request);

        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'name', $variantName));
    }

    /**
     * @Then I should see empty list of products
     */
    public function iShouldSeeEmptyListOfProducts(): void
    {
        Assert::same($this->responseChecker->countTotalCollectionItems($this->client->getLastResponse()), 0);
    }

    /**
     * @Then I should see :count products in the list
     */
    public function iShouldSeeProductsInTheList(int $count): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then they should have order like :firstProductName, :secondProductName and :thirdProductName
     */
    public function theyShouldHaveOrderLikeAnd(string ...$productNames): void
    {
        $productNamesFromResponse = new ArrayCollection();

        foreach ($this->responseChecker->getCollection($this->client->getLastResponse()) as $productItem) {
            $productNamesFromResponse->add($productItem['name']);
        }

        foreach ($productNamesFromResponse as $key => $name) {
            Assert::same($name, $productNames[$key]);
        }
    }

    /**
     * @Then /^the product price should be ("[^"]+")$/
     */
    public function theProductPriceShouldBe(int $price): void
    {
        $response = $this->client->getLastResponse();

        $defaultVariantResponse = $this->client->showByIri(
            $this->responseChecker->getValue($response, 'defaultVariant'),
        );

        Assert::same($this->responseChecker->getValue($defaultVariantResponse, 'price'), $price);
    }

    /**
     * @Then I should see the product description :description
     */
    public function iShouldSeeTheProductDescription(string $description): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'description'),
            $description,
        );
    }

    /**
     * @Then /^the visitor should(?:| still) see ("[^"]+") as the (price|original price) of the ("[^"]+" product) in the ("[^"]+" channel)$/
     */
    public function theVisitorShouldSeeAsThePriceOfTheProductInTheChannel(
        int $price,
        string $priceType,
        ProductInterface $product,
        ChannelInterface $channel,
    ): void {
        $this->sharedStorage->set('token', null);
        $this->sharedStorage->set('hostname', $channel->getHostname());
        $this->channelContextSetter->setChannel($channel);

        Assert::true($this->hasProductWithPrice(
            [$this->responseChecker->getResponseContent($this->client->show(Resources::PRODUCTS, $product->getCode()))],
            $price,
            null,
            StringInflector::nameToCamelCase($priceType),
        ));
    }

    /**
     * @Then I should see a main image
     */
    public function iShouldSeeAMainImage(): void
    {
        Assert::true($this->hasProductWithMainImage());
    }

    /**
     * @Then /^I should not be able to select the "([^"]+)" ([^\s]+) option value$/
     */
    public function iShouldNotBeAbleToSelectTheOptionValue(string $optionValue, string $optionName): void
    {
        Assert::false($this->hasProductOptionWithNameAndValue($optionValue, $optionName));
    }

    /**
     * @Then I should be able to select between :count variants
     */
    public function iShouldBeAbleToSelectBetweenVariants(int $count): void
    {
        $response = $this->client->getLastResponse();
        $variants = $this->responseChecker->getValue($response, 'variants');

        Assert::count($variants, $count);
    }

    /**
     * @Then I should not be able to select the :productVariantName variant
     */
    public function iShouldNotBeAbleToSelectTheVariant(string $productVariantName): void
    {
        $response = $this->client->getLastResponse();
        $variants = $this->responseChecker->getValue($response, 'variants');

        Assert::false($this->productHasProductVariantWithName($variants, $productVariantName));
    }

    /**
     * @Then /^I should(?:| also) see the product association "([^"]+)" with (products "[^"]+" and "[^"]+")$/
     */
    public function iShouldSeeTheProductAssociationWithProductsAnd(string $productAssociationName, array $products): void
    {
        /** @var ProductInterface $product */
        $product = $this->sharedStorage->get('product');

        $response = $this->client->show(Resources::PRODUCTS, $product->getCode());
        $associations = $this->responseChecker->getValue($response, 'associations');

        Assert::true($this->hasAssociationsWithProducts($associations, $productAssociationName, $products));
    }

    private function hasProductWithPrice(
        array $products,
        int $price,
        ?string $productCode = null,
        string $priceType = 'price',
    ): bool {
        foreach ($products as $product) {
            if ($productCode !== null && $product['code'] !== $productCode) {
                continue;
            }

            foreach ($product['variants'] as $variantIri) {
                $request = $this->requestFactory->custom($variantIri, HttpRequest::METHOD_GET);
                $response = $this->client->executeCustomRequest($request);

                /** @var int $variantPrice */
                $variantPrice = $this->responseChecker->getValue($response, $priceType);

                if ($price === $variantPrice) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasProductWithName(array $products, string $name): bool
    {
        foreach ($products as $product) {
            if ($product['name'] === $name) {
                return true;
            }
        }

        return false;
    }

    private function hasProductWithNameAndShortDescription(array $products, string $name, string $shortDescription): bool
    {
        foreach ($products as $product) {
            if ($product['name'] === $name && $product['shortDescription'] === $shortDescription) {
                return true;
            }
        }

        return false;
    }

    private function hasProductOptionWithNameAndValue(string $optionValue, string $optionName): bool
    {
        $response = $this->client->getLastResponse();
        $productOptions = $this->responseChecker->getValue($response, 'options');

        foreach ($productOptions as $optionIri) {
            if (!$this->hasProductOptionWithName($optionIri, $optionName)) {
                continue;
            }

            $variants = $this->responseChecker->getValue($response, 'variants');
            foreach ($variants as $variantIri) {
                if ($this->variantHasProductOptionValue($variantIri, $optionValue)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasProductOptionWithName(string $optionIri, string $optionName): bool
    {
        $response = $this->client->showByIri($optionIri);

        return $this->responseChecker->hasValue($response, 'code', StringInflector::nameToUppercaseCode($optionName));
    }

    private function variantHasProductOptionValue(string $variantIri, string $optionValue): bool
    {
        $variants = $this->client->showByIri($variantIri);
        $optionValues = $this->responseChecker->getValue($variants, 'optionValues');

        foreach ($optionValues as $valueIri) {
            if ($this->hasProductOptionWithValue($valueIri, $optionValue)) {
                return true;
            }
        }

        return false;
    }

    private function hasProductOptionWithValue(string $valueIri, string $optionValue): bool
    {
        return $this->responseChecker->hasValue($this->client->ShowByIri($valueIri), 'code', StringInflector::nameToUppercaseCode($optionValue));
    }

    private function productHasProductVariantWithName(array $variants, string $variantName): bool
    {
        foreach ($variants as $variantIri) {
            if ($this->responseChecker->hasValue($this->client->showByIri($variantIri), 'name', $variantName)) {
                return true;
            }
        }

        return false;
    }

    private function hasProductWithMainImage(): bool
    {
        $images = $this->responseChecker->getValue($this->client->getLastResponse(), 'images');

        return $images[0]['type'] === 'main' && $images[0]['path'];
    }

    private function hasAssociationsWithProducts(
        array $associationsIris,
        string $productAssociationTypeName,
        array $products,
    ): bool {
        try {
            $associatedProducts = $this->provideAssociatedProductsOfAssociationTypeName($associationsIris, $productAssociationTypeName);
        } catch (\InvalidArgumentException) {
            return false;
        }

        foreach ($products as $product) {
            if (!$this->isProductAssociated($product, $associatedProducts)) {
                return false;
            }
        }

        return true;
    }

    private function provideAssociatedProductsOfAssociationTypeName(
        array $associationsIris,
        string $productAssociationTypeName,
    ): array {
        foreach ($associationsIris as $associationIri) {
            $associationResponse = $this->client->showByIri($associationIri);
            $associationTypeIri = $this->responseChecker->getValue($associationResponse, 'type');
            $associationTypeResponse = $this->client->showByIri($associationTypeIri);

            if ($this->responseChecker->hasValue($associationTypeResponse, 'name', $productAssociationTypeName)) {
                return $this->responseChecker->getValue($associationResponse, 'associatedProducts');
            }
        }

        throw new \InvalidArgumentException(sprintf('There is no product association with name %s.', $productAssociationTypeName));
    }

    private function isProductAssociated(ProductInterface $product, array $associatedProducts): bool
    {
        $productIri = $this->iriConverter->getIriFromItem($product);

        return in_array($productIri, $associatedProducts, true);
    }
}
