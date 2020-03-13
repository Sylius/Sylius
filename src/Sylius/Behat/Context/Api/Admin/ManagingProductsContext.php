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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ManagingProductsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ApiClientInterface */
    private $productReviewClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    public function __construct(
        ApiClientInterface $client,
        ApiClientInterface $productReviewClient,
        ResponseCheckerInterface $responseChecker
    ) {
        $this->client = $client;
        $this->productReviewClient = $productReviewClient;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I want to create a new configurable product
     */
    public function iWantToCreateANewConfigurableProduct(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(string $code = null): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I name it :name in :localeCode
     * @When I rename it to :name in :localeCode
     */
    public function iRenameItToIn(string $name, string $localeCode): void
    {
        $data = [
            'translations' => [
                $localeCode => [
                    'locale' => $localeCode,
                    'name' => $name,
                ],
            ],
        ];

        $this->client->updateRequestData($data);
    }

    /**
     * @When I set its slug to :slug
     * @When I set its slug to :slug in :localeCode
     * @When I remove its slug
     */
    public function iSetItsSlugTo(?string $slug = null, $localeCode = 'en_US'): void
    {
        $data = [
            'translations' => [
                $localeCode => [
                    'slug' => $slug,
                ],
            ],
        ];

        $this->client->updateRequestData($data);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I filter them by :taxon taxon
     */
    public function iFilterThemByTaxon(TaxonInterface $taxon): void
    {
        $this->client->buildFilter(['productTaxons.taxon.code' => $taxon->getCode()]);
        $this->client->filter();
    }

    /**
     * @Then I should see the product :productName in the list
     * @Then the product :productName should appear in the store
     * @Then the product :productName should be in the shop
     * @Then this product should still be named :productName
     */
    public function theProductShouldAppearInTheShop(string $productName): void
    {
        $this->client->index();

        Assert::true(
            $this
                ->responseChecker
                ->hasItemWithTranslation($this->client->getLastResponse(),'en_US', 'name', $productName)
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true($this->responseChecker->isCreationSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true($this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Given I am browsing products
     * @When I browse products
     * @When I want to browse products
     */
    public function iWantToBrowseProducts(): void
    {
        $this->client->index();
    }

    /**
     * @When I delete the :product product
     * @When I try to delete the :product product
     */
    public function iDeleteProduct(ProductInterface $product): void
    {
        $this->client->delete($product->getCode());
    }

    /**
     * @Then I should see :numberOfProducts products in the list
     */
    public function iShouldSeeProductsInTheList(int $count): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), $count);
    }

    /**
     * @Then I should( still) see a product with :field :value
     */
    public function iShouldSeeProductWith(string $field, string $value): void
    {
        Assert::true(
            $this
                ->responseChecker
                ->hasItemWithTranslation($this->client->getLastResponse(), 'en_US', $field, $value)
        );
    }

    /**
     * @Then I should not see any product with :field :value
     */
    public function iShouldNotSeeAnyProductWith(string $field, string $value): void
    {
        Assert::false(
            $this
                ->responseChecker
                ->hasItemWithTranslation($this->client->getLastResponse(), 'en_US', $field, $value)
        );
    }

    /**
     * @Then /^(this product) should not exist in the product catalog$/
     */
    public function productShouldNotExist(ProductInterface $product): void
    {
        $this->client->index();
        Assert::false(
            $this
                ->responseChecker
                ->hasItemWithValue($this->client->getLastResponse(), 'code', $product->getCode())
        );
    }

    /**
     * @Then /^there should be no reviews of (this product)$/
     */
    public function thereAreNoProductReviews(ProductInterface $product): void
    {
        $this->productReviewClient->index();
        Assert::true(
            empty(
                $this
                    ->responseChecker
                    ->getCollectionItemsWithValue(
                        $this->productReviewClient->getLastResponse(),
                        'reviewSubject',
                        '/new-api/products/' . $product->getCode()
                )
            )
        );
    }
}
