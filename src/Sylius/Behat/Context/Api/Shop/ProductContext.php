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

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    private ApiClientInterface $client;

    private ResponseCheckerInterface $responseChecker;

    private SharedStorageInterface $sharedStorage;

    private IriConverterInterface $iriConverter;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage,
        IriConverterInterface $iriConverter
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @When /^I check (this product)'s details$/
     * @When I view product :product
     */
    public function iOpenProductPage(ProductInterface $product): void
    {
        $this->client->show($product->getCode());
        $this->sharedStorage->set('productVariant', current($product->getVariants()->getValues()));
    }

    /**
     * @When I browse products from taxon :taxon
     */
    public function iBrowseProductsFromTaxon(TaxonInterface $taxon): void
    {
        $this->client->index();
        $this->client->addFilter('taxon', $this->iriConverter->getIriFromItem($taxon));
        $this->client->filter();
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
    public function iSearchForProductsWithName(string $name)
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
            $name
        ));
    }

    /**
     * @Then I should see that it is out of stock
     */
    public function iShouldSeeItIsOutOfStock(): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->sharedStorage->get('productVariant');

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
            $name
        ));
    }

    /**
     * @Then /^I should see the product price ("[^"]+")$/
     */
    public function iShouldSeeTheProductPrice(int $price): void
    {
        Assert::true(
            $this->hasProductWithPrice(
                [$this->responseChecker->getResponseContent($this->client->getLastResponse())],
                $price,
            )
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
                $product->getCode()
            ),
            sprintf('There is no product with %s code and %s price', $product->getCode(), $price)
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
                $shortDescription
            ),
            sprintf('There is no product with %s name and %s short description', $product->getName(), $shortDescription)
        );
    }

    /**
     * @When I browse products
     */
    public function iViewProducts(): void
    {
        $this->client->index();
    }

    /**
     * @Then the first product on the list should have name :name
     */
    public function theFirstProductOnTheListShouldHaveName(string $name): void
    {
        $products = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same($products[0]['translations']['en_US']['name'], $name);
    }

    /**
     * @Then the last product on the list should have name :name
     */
    public function theLastProductOnTheListShouldHaveName(string $name): void
    {
        $products = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same(end($products)['translations']['en_US']['name'], $name);
    }

    /**
     * @When /^I should see only (\d+) product(s)$/
     */
    public function iShouldSeeOnlyProducts(int $count): void
    {
        Assert::same(
            count($this->responseChecker->getCollection($this->client->getLastResponse())),
            $count,
            'Number of products from response is different then expected'
        );
    }

    /**
     * @Then I should not see the product with name :name
     */
    public function iShouldNotSeeProductWithName(string $name): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithTranslation(
                $this->client->getLastResponse(),
                'en_US',
                'name',
                $name
            )
        );
    }

    /**
     * @Then I should see the product name :name
     */
    public function iShouldSeeProductName(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithTranslation(
                $this->client->getLastResponse(),
                'en_US',
                'name',
                $name
            )
        );

        Assert::same($this->responseChecker->getTranslationValue($this->client->getLastResponse(), 'name'), $name);
    }

    /**
     * @Then its current variant should be named :variantName
     */
    public function itsCurrentVariantShouldBeNamed(string $variantName): void
    {
        $response = $this->client->getLastResponse();

        $productVariant = $this->responseChecker->getValue($response, 'variants');
        $this->client->executeCustomRequest(Request::custom($productVariant[0], HttpRequest::METHOD_GET));

        Assert::true(
            $this->responseChecker->hasTranslation(
                $this->client->getLastResponse(),
                'en_US',
                'name',
                $variantName
            )
        );
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
            $productNamesFromResponse->add($productItem['translations']['en_US']['name']);
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
        $defaultVariantResponse = $this->client->showByIri(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'defaultVariant')
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
            $description
        );

        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'translations')['en_US']['description'],
            $description
        );
    }

    private function hasProductWithPrice(array $products, int $price, ?string $productCode = null): bool
    {
        foreach ($products as $product) {
            if ($productCode !== null && $product['code'] !== $productCode) {
                continue;
            }

            foreach ($product['variants'] as $variantIri) {
                $this->client->executeCustomRequest(Request::custom($variantIri, HttpRequest::METHOD_GET));

                /** @var int $variantPrice */
                $variantPrice = $this->responseChecker->getValue($this->client->getLastResponse(), 'price');

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
            foreach ($product['translations'] as $translation) {
                if ($translation['name'] === $name) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasProductWithNameAndShortDescription(array $products, string $name, string $shortDescription): bool
    {
        foreach ($products as $product) {
            foreach ($product['translations'] as $translation) {
                if ($translation['name'] === $name && $translation['shortDescription'] === $shortDescription) {
                    return true;
                }
            }
        }

        return false;
    }
}
