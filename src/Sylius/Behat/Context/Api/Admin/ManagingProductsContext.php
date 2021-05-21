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

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductOption;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class ManagingProductsContext implements Context
{
    public const SORT_TYPES = ['ascending' => 'asc', 'descending' => 'desc'];

    /** @var ApiClientInterface */
    private $client;

    /** @var ApiClientInterface */
    private $adminUsersClient;

    /** @var ApiClientInterface */
    private $productReviewsClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var IriConverterInterface */
    private $iriConverter;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ApiClientInterface $adminUsersClient,
        ApiClientInterface $productReviewsClient,
        ResponseCheckerInterface $responseChecker,
        IriConverterInterface $iriConverter,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->adminUsersClient = $adminUsersClient;
        $this->productReviewsClient = $productReviewsClient;
        $this->responseChecker = $responseChecker;
        $this->iriConverter = $iriConverter;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I start sorting products by name
     * @When I sort the products :sortType by name
     * @When I switch the way products are sorted :sortType by name
     * @Given the products are already sorted :sortType by name
     */
    public function iStartSortingProductsByName(string $sortType = 'ascending'): void
    {
        $this->client->sort([
            'translation.name' => self::SORT_TYPES[$sortType],
            'localeCode' => $this->getAdminLocaleCode(),
        ]);
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
     * @When I change my locale to :localeCode
     */
    public function iSwitchTheLocaleToTheLocale(string $localeCode): void
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->sharedStorage->get('administrator');

        $this->adminUsersClient->buildUpdateRequest((string) $adminUser->getId());

        $this->adminUsersClient->updateRequestData(['localeCode' => $localeCode]);
        $this->adminUsersClient->update();
    }

    /**
     * @When I want to create a new configurable product
     * @When I want to create a new simple product
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
     * @When I do not name it
     */
    public function iRenameItToIn(?string $name = null, string $localeCode = 'en_US'): void
    {
        $data['translations'][$localeCode]['locale'] = $localeCode;

        if ($name !== null) {
            $data['translations'][$localeCode]['name'] = $name;
        }

        $this->client->updateRequestData($data);
    }

    /**
     * @When /^I set its(?:| default) price to "(?:€|£|\$)([^"]+)" for ("[^"]+" channel)$/
     */
    public function iSetItsPriceTo(string $price, ChannelInterface $channel): void
    {
        $localeCode = $channel->getLocales()->first()->getCode();

        $data = [
            'translations' => [
                $localeCode => [
                    'locale' => $localeCode,
                    'price' => $price,
                ],
            ],
            'channel' => $this->iriConverter->getIriFromItem($channel),
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
                    'locale' => $localeCode,
                    'slug' => $slug,
                ],
            ],
        ];

        $this->client->updateRequestData($data);
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I add the :productOption option to it
     */
    public function iAddTheOptionToIt(ProductOption $productOption): void
    {
        /** @var ProductInterface $product */
        $product = $this->sharedStorage->get('product');

        $productOptions = $this->responseChecker->getValue($this->client->show($product->getCode()), 'options');

        $productOptions[] = $this->iriConverter->getIriFromItem($productOption);

        $this->client->updateRequestData(['options' => $productOptions]);
    }

    /**
     * @When /^I choose main (taxon "[^"]+")$/
     */
    public function iChooseMainTaxon(TaxonInterface $taxon): void
    {
        $this->client->updateRequestData(['mainTaxon' => $this->iriConverter->getIriFromItem($taxon)]);
    }

    /**
     * @When I (try to) save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @When I filter them by :taxon taxon
     */
    public function iFilterThemByTaxon(TaxonInterface $taxon): void
    {
        $this->client->addFilter('productTaxons.taxon.code', $taxon->getCode());
        $this->client->filter();
    }

    /**
     * @When I start sorting products by code
     * @When I switch the way products are sorted :sortType by code
     */
    public function iSwitchTheWayProductsAreSortedByCode(string $sortType = 'ascending'): void
    {
        $this->client->sort(['code' => self::SORT_TYPES[$sortType]]);
    }

    /**
     * @When I (try to) delete the :product product
     */
    public function iDeleteProduct(ProductInterface $product): void
    {
        $this->client->delete($product->getCode());
    }

    /**
     * @When /^I want to modify (this product)$/
     * @When I (want to) modify the :product product
     */
    public function iWantToModifyAProduct(ProductInterface $product): void
    {
        $this->client->buildUpdateRequest($product->getCode());
    }

    /**
     * @When I enable slug modification
     * @When I enable slug modification in :localeCode
     */
    public function iEnableSlugModification(string $localeCode = 'en_US'): void
    {
        $data['translations'][$localeCode]['slug'] = '';

        $this->client->updateRequestData($data);
    }

    /**
     * @Then I should see the product :productName in the list
     * @Then the product :productName should appear in the store
     * @Then the product :productName should be in the shop
     * @Then this product should still be named :productName
     */
    public function theProductShouldAppearInTheShop(string $productName): void
    {
        $response = $this->client->index();

        Assert::true(
            $this->responseChecker->hasItemWithTranslation($response, 'en_US', 'name', $productName)
        );
    }

    /**
     * @When I remove its name from :localeCode translation
     */
    public function iRemoveItsNameFromTranslation(string $localeCode): void
    {
        $this->client->updateRequestData([
            'translations' => [
                $localeCode => [
                    'name' => '',
                    'locale' => $localeCode,
                ],
            ],
        ]);
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        $res = $this->client->getLastResponse();
        Assert::true($this->responseChecker->isCreationSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Product could not be edited'
        );
    }

    /**
     * @Then I should be notified that this product is in use and cannot be deleted
     */
    public function iShouldBeNotifiedThatThisProductIsInUseAndCannotBeDeleted(): void
    {
        Assert::false(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Product can be deleted, but it should not'
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Product still exists, but it should not'
        );
    }

    /**
     * @Then /^I should be notified that (code|name) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Please enter product %s.', $element)
        );
    }

    /**
     * @Then I should be notified that code has to be unique
     */
    public function iShouldBeNotifiedThatCodeHasToBeUnique(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Product code must be unique.'
        );
    }

    /**
     * @Then I should see :count products in the list
     */
    public function iShouldSeeProductsInTheList(int $count): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), $count);
    }

    /**
     * @Then I should see a product with :field :value
     */
    public function iShouldSeeProductWith(string $field, string $value): void
    {
        Assert::true(
            $this->hasProductWithFieldValue($this->client->getLastResponse(), $field, $value),
            sprintf('Product has not %s with %s', $field, $value)
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
                ->hasItemWithTranslation($this->client->getLastResponse(), 'en_US', $field, $value),
            sprintf('Product with %s set as %s still exists, but it should not', $field, $value)
        );
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->addRequestData('code', '_NEW');
        $this->client->update();
        $this->client->index();

        Assert::false(
            $this->responseChecker->hasItemOnPositionWithValue(
                $this->client->getLastResponse(),
                0,
                'code',
                '/api/v2/admin/products/_NEW'
            ),
            sprintf('It was possible to change %s', '_NEW')
        );
    }

    /**
     * @Then /^(this product) main (taxon should be "[^"]+")$/
     * @Then main taxon of product :product should be :taxon
     */
    public function thisProductMainTaxonShouldBe(ProductInterface $product, TaxonInterface $taxon): void
    {
        $response = $this->client->show($product->getCode());

        $mainTaxon = $this->responseChecker->getValue($response, 'mainTaxon');

        Assert::same($mainTaxon, $this->iriConverter->getIriFromItem($taxon));
    }

    /**
     * @Then /^(this product) name should be "([^"]+)"$/
     */
    public function thisProductNameShouldBe(ProductInterface $product, string $name): void
    {
        $response = $this->client->show($product->getCode());

        Assert::true(
            $this->responseChecker->hasTranslation($response, 'en_US', 'name', $name),
            sprintf('Product\'s name %s does not exist', $name)
        );
    }

    /**
     * @Then /^(this product) should not exist in the product catalog$/
     */
    public function productShouldNotExist(ProductInterface $product): void
    {
        $response = $this->client->index();

        Assert::false(
            $this->responseChecker->hasItemWithValue($response, 'code', $product->getCode()),
            sprintf('Product with name %s still exists, but it should not', $product->getName())
        );
    }

    /**
     * @Then /^(this product) should have (?:a|an) ("[^"]+" option)$/
     */
    public function thisProductShouldHaveOption(ProductInterface $product, ProductOptionInterface $productOption): void
    {
        $response = $this->client->show($product->getCode());

        $productFromResponse = $this->responseChecker->getResponseContent($response);

        Assert::true(
            in_array($this->iriConverter->getIriFromItem($productOption), $productFromResponse['options'], true),
            sprintf('Product with option %s does not exist', $productOption->getName())
        );
    }

    /**
     * @Then the first product on the list should have :field :value
     */
    public function theFirstProductOnTheListShouldHave(string $field, string $value): void
    {
        $products = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same($this->getFieldValueOfFirstProduct($products[0], $field), $value);
    }

    /**
     * @Then /^the slug of the ("[^"]+" product) should(?:| still) be "([^"]+)"$/
     * @Then /^the slug of the ("[^"]+" product) should(?:| still) be "([^"]+)" (in the "[^"]+" locale)$/
     */
    public function productSlugShouldBe(ProductInterface $product, string $slug, $localeCode = 'en_US'): void
    {
        $response = $this->client->show($product->getCode());

        Assert::true(
            $this->responseChecker->hasTranslation($response, $localeCode, 'slug', $slug),
            sprintf('Product\'s slug %s does not exist', $slug)
        );
    }

    /**
     * @Then /^there should be no reviews of (this product)$/
     */
    public function thereAreNoProductReviews(ProductInterface $product): void
    {
        $response = $this->productReviewsClient->index();

        Assert::isEmpty(
            $this->responseChecker->getCollectionItemsWithValue(
                $response,
                'reviewSubject',
                $this->iriConverter->getIriFromItem($product)
            ),
            'Should be no reviews, but some exist'
        );
    }

    /**
     * @Then /^(this product) should still exist in the product catalog$/
     */
    public function productShouldExistInTheProductCatalog(ProductInterface $product): void
    {
        $response = $this->client->index();
        $code = $product->getCode();

        Assert::true(
            $this->responseChecker->hasItemWithValue($response, 'code', $code),
            sprintf('Product with code %s does not exist', $code)
        );
    }

    /**
     * @Then /^the (product "[^"]+") should still have an accessible image$/
     */
    public function productShouldStillHaveAnAccessibleImage(ProductInterface $product): void
    {
        $response = $this->client->show($product->getCode());

        Assert::true($this->hasProductImage($response, $product), 'Image does not exists');
    }

    /**
     * @Then /^product with (name|code) "([^"]+)" should not be added$/
     */
    public function productWithNameShouldNotBeAdded(string $field, string $value): void
    {
        Assert::false($this->hasProductWithFieldValue($this->client->index(), $field, $value));
    }

    private function getAdminLocaleCode(): string
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->sharedStorage->get('administrator');

        $response = $this->adminUsersClient->show((string) $adminUser->getId());

        return $this->responseChecker->getValue($response, 'localeCode');
    }

    private function getFieldValueOfFirstProduct(array $product, string $field): ?string
    {
        if ($field === 'code') {
            return $product['code'];
        }

        if ($field === 'name') {
            return $product['translations'][$this->getAdminLocaleCode()]['name'];
        }

        return null;
    }

    private function hasProductImage(Response $response, ProductInterface $product): bool
    {
        $productFromResponse = $this->responseChecker->getResponseContent($response);

        return
            isset($productFromResponse['images'][0]) &&
            $productFromResponse['images'][0]['path'] === $product->getImages()->first()->getPath()
        ;
    }

    private function hasProductWithFieldValue(Response $response, string $field, string $value): bool
    {
        if ($field === 'code') {
            return $this->responseChecker->hasItemWithValue($response, $field, $value);
        }

        if ($field === 'name') {
            return $this->responseChecker->hasItemWithTranslation($response, $this->getAdminLocaleCode(), $field, $value);
        }

        return false;
    }
}
