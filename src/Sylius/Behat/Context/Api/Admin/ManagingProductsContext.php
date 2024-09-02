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
use Sylius\Behat\Context\Api\Admin\Helper\ValidationTrait;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Converter\SectionAwareIriConverterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class ManagingProductsContext implements Context
{
    use ValidationTrait;

    public const SORT_TYPES = ['ascending' => 'asc', 'descending' => 'desc'];

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
        private SharedStorageInterface $sharedStorage,
        private string $apiUrlPrefix,
    ) {
    }

    /**
     * @Given the products are already sorted :sortType by name
     * @When I start sorting products by name
     * @When I sort the products :sortType by name
     * @When I switch the way products are sorted :sortType by name
     */
    public function iStartSortingProductsByName(string $sortType = 'ascending'): void
    {
        $this->client->sort([
            'translation.name' => self::SORT_TYPES[$sortType],
            'localeCode' => $this->getAdminLocaleCode(),
        ]);

        $this->sharedStorage->set('response', $this->client->getLastResponse());
    }

    /**
     * @Given I am browsing products
     * @When I browse products
     * @When I want to browse products
     */
    public function iWantToBrowseProducts(): void
    {
        $this->client->index(Resources::PRODUCTS);

        $this->sharedStorage->set('response', $this->client->getLastResponse());
    }

    /**
     * @When I change my locale to :localeCode
     */
    public function iSwitchTheLocaleToTheLocale(string $localeCode): void
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->sharedStorage->get('administrator');

        $this->client->buildUpdateRequest(Resources::ADMINISTRATORS, (string) $adminUser->getId());

        $this->client->updateRequestData(['localeCode' => $localeCode]);
        $this->client->update();
    }

    /**
     * @When I want to create a new configurable product
     */
    public function iWantToCreateANewConfigurableProduct(): void
    {
        $this->client->buildCreateRequest(Resources::PRODUCTS);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
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
        $data['translations'][$localeCode] = [];

        if ($name !== null) {
            $data['translations'][$localeCode]['name'] = $name;
        }

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
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I add the :productOption option to it
     */
    public function iAddTheOptionToIt(ProductOptionInterface $productOption): void
    {
        $this->client->updateRequestData(['options' => [$this->sectionAwareIriConverter->getIriFromResourceInSection($productOption, 'admin')]]);
    }

    /**
     * @When /^I choose main (taxon "[^"]+")$/
     */
    public function iChooseMainTaxon(TaxonInterface $taxon): void
    {
        $this->client->updateRequestData(['mainTaxon' => $this->sectionAwareIriConverter->getIriFromResourceInSection($taxon, 'admin')]);
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

        $this->sharedStorage->set('response', $this->client->getLastResponse());
    }

    /**
     * @When I (try to) delete the :product product
     */
    public function iDeleteProduct(ProductInterface $product): void
    {
        $this->client->delete(Resources::PRODUCTS, $product->getCode());
    }

    /**
     * @When /^I want to modify (this product)$/
     * @When I (want to) modify the :product product
     */
    public function iWantToModifyAProduct(ProductInterface $product): void
    {
        $this->client->buildUpdateRequest(Resources::PRODUCTS, $product->getCode());
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
        $response = $this->client->index(Resources::PRODUCTS);

        Assert::true(
            $this->responseChecker->hasItemWithTranslation($response, 'en_US', 'name', $productName),
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
                ],
            ],
        ]);
    }

    /**
     * @When I set its meta keywords to too long string in :localeCode
     */
    public function iSetItsMetaKeywordsToTooLongStringIn(string $localeCode): void
    {
        $this->client->updateRequestData([
            'translations' => [
                $localeCode => [
                    'metaKeywords' => str_repeat('a', 256),
                ],
            ],
        ]);
    }

    /**
     * @When I set its meta description to too long string in :localeCode
     */
    public function iSetItsMetaDescriptionToTooLongStringIn(string $localeCode): void
    {
        $this->client->updateRequestData([
            'translations' => [
                $localeCode => [
                    'metaDescription' => str_repeat('a', 256),
                ],
            ],
        ]);
    }

    /**
     * @When I set its non-translatable :attribute attribute to :value
     */
    public function iSetItsNonTranslatableAttributeTo(ProductAttributeInterface $attribute, string $value): void
    {
        $this->client->addSubResourceData(
            'attributes',
            [
                'attribute' => $this->iriConverter->getIriFromResource($attribute),
                'value' => $this->getAttributeValueInProperType($attribute, $value),
            ],
        );
    }

    /**
     * @When I set the invalid integer value of the non-translatable :attribute attribute to :value
     */
    public function iSetTheInvalidIntegerValueOfTheNonTranslatableAttributeTo(ProductAttributeInterface $attribute, int $value): void
    {
        $this->client->addSubResourceData(
            'attributes',
            [
                'attribute' => $this->iriConverter->getIriFromResource($attribute),
                'value' => $value,
            ],
        );
    }

    /**
     * @When I set the invalid string value of the non-translatable :attribute attribute to :value
     */
    public function iSetTheInvalidStringValueOfTheNonTranslatableAttributeTo(ProductAttributeInterface $attribute, string $value): void
    {
        $this->client->addSubResourceData(
            'attributes',
            [
                'attribute' => $this->iriConverter->getIriFromResource($attribute),
                'value' => $value,
            ],
        );
    }

    /**
     * @When I set its :attribute attribute to :value
     * @When I set its :attribute attribute to :value in :localeCode
     * @When I do not set its :attribute attribute in :localeCode
     */
    public function iSetItsAttributeTo(
        ProductAttributeInterface $attribute,
        ?string $value = null,
        string $localeCode = 'en_US',
    ): void {
        $this->client->addSubResourceData(
            'attributes',
            [
                'attribute' => $this->sectionAwareIriConverter->getIriFromResourceInSection($attribute, 'admin'),
                'value' => $value !== null ? $this->getAttributeValueInProperType($attribute, $value) : null,
                'localeCode' => $localeCode,
            ],
        );
    }

    /**
     * @When I remove its :attribute attribute
     */
    public function iRemoveItsAttribute(ProductAttributeInterface $attribute): void
    {
        $attributeIri = $this->sectionAwareIriConverter->getIriFromResourceInSection($attribute, 'admin');

        $content = $this->client->getContent();
        foreach ($content['attributes'] as $key => $attributeValue) {
            if ($attributeValue['attribute'] === $attributeIri) {
                unset($content['attributes'][$key]);
            }
        }

        $this->client->setRequestData($content);
    }

    /**
     * @When I add the :attributeName attribute
     */
    public function iAddTheAttribute(string $attributeName): void
    {
        // Intentionally left blank
    }

    /**
     * @When I select :value value in :localeCode for the :attribute attribute
     */
    public function iSelectValueInForTheAttribute(
        string $value,
        string $localeCode,
        ProductAttributeInterface $attribute,
    ): void {
        $this->client->addSubResourceData(
            'attributes',
            [
                'attribute' => $this->iriConverter->getIriFromResource($attribute),
                'value' => [$this->getSelectAttributeValueUuidByChoiceValue($attribute, $value)],
                'localeCode' => $localeCode,
            ],
        );
    }

    /**
     * @When I select :value value for the :attribute attribute
     */
    public function iSelectValueForTheAttribute(
        string $value,
        ProductAttributeInterface $attribute,
    ): void {
        $this->client->addSubResourceData(
            'attributes',
            [
                'attribute' => $this->iriConverter->getIriFromResource($attribute),
                'value' => [$this->getSelectAttributeValueUuidByChoiceValue($attribute, $value)],
            ],
        );
    }

    /**
     * @When I assign it to channel :channel
     */
    public function iAssignItToChannel(ChannelInterface $channel): void
    {
        $this->client->addRequestData('channels', [$this->iriConverter->getIriFromResource($channel)]);
    }

    /**
     * @When I access the :product product
     */
    public function iAccessTheProduct(ProductInterface $product): void
    {
        $this->client->show(Resources::PRODUCTS, $product->getCode());
    }

    /**
     * @When I choose :channel as a channel filter
     */
    public function iChooseChannelAsAChannelFilter(ChannelInterface $channel): void
    {
        $this->client->addFilter('channel', $this->iriConverter->getIriFromResource($channel));
    }

    /**
     * @When I save my changes to the images
     */
    public function iSaveMyChangesToTheImages(): void
    {
        // Intentionally left blank
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->client->filter();

        $this->sharedStorage->set('response', $this->client->getLastResponse());
    }

    /**
     * @Then I should see main taxon is :taxon
     */
    public function iShouldSeeMainTaxonIs(TaxonInterface $taxon): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'mainTaxon'),
            $this->sectionAwareIriConverter->getIriFromResourceInSection($taxon, 'admin'),
        );
    }

    /**
     * @Then I should see product taxon :taxon
     */
    public function iShouldSeeProductTaxon(TaxonInterface $taxon): void
    {
        $product = $this->sharedStorage->get('product');
        Assert::isInstanceOf($product, ProductInterface::class);
        $productTaxon = $product->getProductTaxons()->filter(
            fn (ProductTaxonInterface $productTaxon) => $productTaxon->getTaxon()->getCode() === $taxon->getCode(),
        )->first();
        Assert::isInstanceOf($productTaxon, ProductTaxonInterface::class);

        Assert::true($this->responseChecker->hasValueInCollection(
            $this->client->getLastResponse(),
            'productTaxons',
            $this->sectionAwareIriConverter->getIriFromResourceInSection($productTaxon, 'admin'),
        ));
    }

    /**
     * @Then I should see option :productOption
     */
    public function iShouldSeeOption(ProductOptionInterface $productOption): void
    {
        Assert::true($this->responseChecker->hasValueInCollection(
            $this->client->getLastResponse(),
            'options',
            $this->sectionAwareIriConverter->getIriFromResourceInSection($productOption, 'admin'),
        ));
    }

    /**
     * @Then I should see :count variants
     */
    public function iShouldSeeVariants(int $count): void
    {
        Assert::count(
            $this->responseChecker->getResponseContent($this->client->getLastResponse())['variants'] ?? [],
            $count,
        );
    }

    /**
     * @Then I should see the :variant variant
     */
    public function iShouldSeeTheVariant(ProductVariantInterface $variant): void
    {
        Assert::true($this->responseChecker->hasValueInCollection(
            $this->client->getLastResponse(),
            'variants',
            $this->sectionAwareIriConverter->getIriFromResourceInSection($variant, 'admin'),
        ));
    }

    /**
     * @Then I should see product :field is :value
     * @Then I should see product's :field is :value
     */
    public function iShouldSeeProductFieldIs(string $field, string $value): void
    {
        $this->assertResponseHasTranslationFieldWithValue($field, $value);
    }

    /**
     * @Then I should see product's meta keyword(s) is/are :metaKeywords
     */
    public function iShouldSeeProductMetaKeywordsAre(string $metaKeywords): void
    {
        $this->assertResponseHasTranslationFieldWithValue('metaKeywords', $metaKeywords);
    }

    /**
     * @Then I should see product's short description is :shortDescription
     */
    public function iShouldSeeProductShortDescriptionIs(string $shortDescription): void
    {
        $this->assertResponseHasTranslationFieldWithValue('shortDescription', $shortDescription);
    }

    /**
     * @Then I should see product association type :productAssociationType
     */
    public function iShouldSeeProductAssociationType(ProductAssociationTypeInterface $productAssociationType): void
    {
        $associations = $this->responseChecker->getValue($this->client->getLastResponse(), 'associations');
        foreach ($associations as $associationIri) {
            /** @var ProductAssociationInterface $association */
            $association = $this->iriConverter->getResourceFromIri($associationIri);
            if ($association->getType()->getCode() === $productAssociationType->getCode()) {
                return;
            }
        }

        throw new \InvalidArgumentException(
            sprintf('Product association type "%s" not found.', $productAssociationType->getCode()),
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
     * @Then I should be notified that this product is in use and cannot be deleted
     */
    public function iShouldBeNotifiedThatThisProductIsInUseAndCannotBeDeleted(): void
    {
        Assert::false(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Product can be deleted, but it should not',
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Product still exists, but it should not',
        );
    }

    /**
     * @Then /^I should be notified that (code|name) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Please enter product %s.', $element),
        );
    }

    /**
     * @Then I should be notified that meta keywords are too long
     */
    public function iShouldBeNotifiedThatMetaKeywordsAreTooLong(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Product meta keywords must not be longer than 255 characters.',
        );
    }

    /**
     * @Then I should be notified that meta description is too long
     */
    public function iShouldBeNotifiedThatMetaDescriptionIsTooLong(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Product meta description must not be longer than 255 characters.',
        );
    }

    /**
     * @Then I should be notified that code has to be unique
     */
    public function iShouldBeNotifiedThatCodeHasToBeUnique(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Product code must be unique.',
        );
    }

    /**
     * @Then I should see a single product in the list
     * @Then I should see :count products in the list
     */
    public function iShouldSeeProductsInTheList(int $count = 1): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), $count);
    }

    /**
     * @Then I should see a product with :field :value
     */
    public function iShouldSeeProductWith(string $field, string $value): void
    {
        $response = $this->getLastResponse();

        Assert::true(
            $this->hasProductWithFieldValue($response, $field, $value),
            sprintf('Product has not %s with %s', $field, $value),
        );
    }

    /**
     * @Then I should not see any product with :field :value
     */
    public function iShouldNotSeeAnyProductWith(string $field, string $value): void
    {
        $response = $this->getLastResponse();

        Assert::false(
            $this->responseChecker->hasItemWithTranslation($response, 'en_US', $field, $value),
            sprintf('Product with %s set as %s still exists, but it should not', $field, $value),
        );
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->addRequestData('code', '_NEW');
        $this->client->update();
        $this->client->index(Resources::PRODUCTS);

        Assert::false(
            $this->responseChecker->hasItemOnPositionWithValue(
                $this->client->getLastResponse(),
                0,
                'code',
                sprintf('%s/admin/products/_NEW', $this->apiUrlPrefix),
            ),
            sprintf('It was possible to change %s', '_NEW'),
        );
    }

    /**
     * @Then /^(this product) main (taxon should be "[^"]+")$/
     * @Then main taxon of product :product should be :taxon
     */
    public function thisProductMainTaxonShouldBe(ProductInterface $product, TaxonInterface $taxon): void
    {
        $response = $this->client->show(Resources::PRODUCTS, $product->getCode());

        $mainTaxon = $this->responseChecker->getValue($response, 'mainTaxon');

        Assert::same($mainTaxon, $this->sectionAwareIriConverter->getIriFromResourceInSection($taxon, 'admin'));
    }

    /**
     * @Then the product :product should have the :taxon taxon
     */
    public function thisProductTaxonShouldBe(ProductInterface $product, TaxonInterface $taxon): void
    {
        $this->client->index(Resources::PRODUCT_TAXONS);
        Assert::true(
            $this->responseChecker->hasItemWithValues($this->client->getLastResponse(), [
                'product' => $this->sectionAwareIriConverter->getIriFromResourceInSection($product, 'admin'),
                'taxon' => $this->sectionAwareIriConverter->getIriFromResourceInSection($taxon, 'admin'),
            ]),
        );
    }

    /**
     * @Then /^(this product) name should be "([^"]+)"$/
     */
    public function thisProductNameShouldBe(ProductInterface $product, string $name): void
    {
        $response = $this->client->show(Resources::PRODUCTS, $product->getCode());

        Assert::true(
            $this->responseChecker->hasTranslation($response, 'en_US', 'name', $name),
            sprintf('Product\'s name %s does not exist', $name),
        );
    }

    /**
     * @Then /^(this product) should not exist in the product catalog$/
     */
    public function productShouldNotExist(ProductInterface $product): void
    {
        $response = $this->client->index(Resources::PRODUCTS);

        Assert::false(
            $this->responseChecker->hasItemWithValue($response, 'code', $product->getCode()),
            sprintf('Product with name %s still exists, but it should not', $product->getName()),
        );
    }

    /**
     * @Then /^(this product) should have (?:a|an) ("[^"]+" option)$/
     */
    public function thisProductShouldHaveOption(ProductInterface $product, ProductOptionInterface $productOption): void
    {
        $response = $this->client->show(Resources::PRODUCTS, $product->getCode());

        $productFromResponse = $this->responseChecker->getResponseContent($response);

        Assert::true(
            in_array($this->sectionAwareIriConverter->getIriFromResourceInSection($productOption, 'admin'), $productFromResponse['options'], true),
            sprintf('Product with option %s does not exist', $productOption->getName()),
        );
    }

    /**
     * @Then the first product on the list should have :field :value
     */
    public function theFirstProductOnTheListShouldHave(string $field, string $value): void
    {
        $products = $this->responseChecker->getCollection($this->getLastResponse());

        Assert::same($this->getFieldValueOfProduct($products[0], $field), $value);
    }

    /**
     * @Then the last product on the list should have name :name
     */
    public function theLastProductOnTheListShouldHaveName(string $name): void
    {
        $products = $this->responseChecker->getCollection($this->getLastResponse());

        Assert::same($this->getFieldValueOfProduct(end($products), 'name'), $name);
    }

    /**
     * @Then /^the (first|last) product on the list shouldn't have a name$/
     */
    public function theProductOnTheListShouldNotHaveAName(string $position): void
    {
        $products = $this->responseChecker->getCollection($this->getLastResponse());

        $product = $position === 'last' ? end($products) : reset($products);

        Assert::null($this->getFieldValueOfProduct($product, 'name'));
    }

    /**
     * @Then /^the slug of the ("[^"]+" product) should(?:| still) be "([^"]+)"$/
     * @Then /^the slug of the ("[^"]+" product) should(?:| still) be "([^"]+)" (in the "[^"]+" locale)$/
     * @Then /^(this product) should(?:| still) have slug "([^"]+)" in ("[^"]+" locale)$/
     */
    public function productSlugShouldBe(ProductInterface $product, string $slug, $localeCode = 'en_US'): void
    {
        $response = $this->client->show(Resources::PRODUCTS, $product->getCode());

        Assert::true(
            $this->responseChecker->hasTranslation($response, $localeCode, 'slug', $slug),
            sprintf('Product\'s slug %s does not exist', $slug),
        );
    }

    /**
     * @Then /^there should be no reviews of (this product)$/
     */
    public function thereAreNoProductReviews(ProductInterface $product): void
    {
        $response = $this->client->index(Resources::PRODUCT_REVIEWS);

        Assert::isEmpty(
            $this->responseChecker->getCollectionItemsWithValue(
                $response,
                'reviewSubject',
                $this->sectionAwareIriConverter->getIriFromResourceInSection($product, 'admin'),
            ),
            'Should be no reviews, but some exist',
        );
    }

    /**
     * @Then /^(this product) should still exist in the product catalog$/
     */
    public function productShouldExistInTheProductCatalog(ProductInterface $product): void
    {
        $response = $this->client->index(Resources::PRODUCTS);
        $code = $product->getCode();

        Assert::true(
            $this->responseChecker->hasItemWithValue($response, 'code', $code),
            sprintf('Product with code %s does not exist', $code),
        );
    }

    /**
     * @Then /^the (product "[^"]+") should still have an accessible image$/
     */
    public function productShouldStillHaveAnAccessibleImage(ProductInterface $product): void
    {
        $response = $this->client->show(Resources::PRODUCTS, $product->getCode());

        Assert::true($this->hasProductImage($response, $product), 'Image does not exists');
    }

    /**
     * @Then /^product with (name|code) "([^"]+)" should not be added$/
     */
    public function productWithNameShouldNotBeAdded(string $field, string $value): void
    {
        Assert::false($this->hasProductWithFieldValue($this->client->index(Resources::PRODUCTS), $field, $value));
    }

    /**
     * @Then non-translatable attribute :attribute of product :product should be :value
     * @Then select attribute :attribute of product :product should be :value
     */
    public function nonTranslatableAttributeOfProductShouldBe(
        ProductAttributeInterface $attribute,
        ProductInterface $product,
        string $value,
    ): void {
        $this->client->show(Resources::PRODUCTS, $product->getCode());

        $this->hasAttributeWithValueInLastResponse($attribute, $value);
    }

    /**
     * @Then I should see non-translatable attribute :attribute with value :value%
     */
    public function iShouldSeeNonTranslatableAttributeWithValue(ProductAttributeInterface $attribute, int $value): void
    {
        $this->hasAttributeWithValueInLastResponse($attribute, (string) ($value / 100));
    }

    /**
     * @Then attribute :attribute of product :product should be :value
     * @Then attribute :attribute of product :product should be :value in :localeCode
     * @Then select attribute :attribute of product :product should be :value in :localeCode
     */
    public function attributeOfProductShouldBe(
        ProductAttributeInterface $attribute,
        ProductInterface $product,
        string $value,
        string $localeCode = 'en_US',
    ): void {
        $this->client->show(Resources::PRODUCTS, $product->getCode());

        $this->hasAttributeWithValueInLastResponse($attribute, $value, $localeCode);
    }

    /**
     * @Then product :product should not have a :attribute attribute
     */
    public function productShouldNotHaveAttribute(ProductInterface $product, ProductAttributeInterface $attribute): void
    {
        $attributes = $this->responseChecker->getValue($this->client->getLastResponse(), 'attributes');
        foreach ($attributes as $attributeValue) {
            if ($attributeValue['attribute'] === $this->sectionAwareIriConverter->getIriFromResourceInSection($attribute, 'admin')) {
                throw new \InvalidArgumentException(
                    sprintf('Product %s have attribute %s', $product->getName(), $attribute->getName()),
                );
            }
        }
    }

    /**
     * @Then I should not be able to edit its options
     */
    public function iShouldNotBeAbleToEditItsOptions(): void
    {
        $productOption = $this->sharedStorage->get('product_option');
        $productOptionIri = $this->sectionAwareIriConverter->getIriFromResourceInSection($productOption, 'admin');
        $this->client->updateRequestData(['options' => [$productOptionIri]]);

        $res = $this->client->update();

        Assert::false(
            $this->responseChecker->hasValueInCollection($res, 'options', $productOptionIri),
            'The product options should not be changed, but they were',
        );
    }

    /**
     * @Then I should be notified that I have to define product variants' prices for newly assigned channels first
     */
    public function iShouldBeNotifiedThatIHaveToDefineProductVariantsPricesForNewlyAssignedChannelsFirst(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'You have to define product variants\' prices for newly assigned channels first.',
        );
    }

    /**
     * @Then I should be notified that slug has to be unique
     */
    public function iShouldBeNotifiedThatSlugHasToBeUnique(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Product slug must be unique.',
        );
    }

    /**
     * @Then I should be notified that I have to define the :attributeName attribute in :localeCode
     */
    public function iShouldBeNotifiedThatIHaveToDefineTheAttributeIn(string $attributeName, string $localeCode): void
    {
        Assert::regex(
            $this->responseChecker->getError($this->client->getLastResponse()),
            '/attributes\[[\d+]\]\.value: This value should not be blank\./',
        );
    }

    /**
     * @Then I should be notified that the :attributeName attribute in :localeCode should be longer than :number
     */
    public function iShouldBeNotifiedThatTheAttributeInShouldBeLongerThan(
        string $attributeName,
        string $localeCode,
        int $number,
    ): void {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('This value is too short. It should have %s characters or more.', $number),
        );
    }

    /**
     * @Then I should be notified that the value of the :attributeName attribute has invalid type
     */
    public function iShouldBeNotifiedThatTheValueOfTheAttributeHasInvalidType(
        string $attributeName,
    ): void {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('The value of attribute "%s" has an invalid type', $attributeName),
        );
    }

    /**
     * @Then I should see an image related to this product
     */
    public function iShouldSeeImageRelatedToThisProduct(): void
    {
        Assert::notEmpty($this->responseChecker->getValue($this->client->getLastResponse(), 'images'));
    }

    /**
     * @Then I should see attribute :attribute with value :value in :locale locale
     */
    public function iShouldSeeAttributeWithValueInLocale(
        ProductAttributeInterface $attribute,
        string $value,
        LocaleInterface $locale,
    ): void {
        $this->hasAttributeWithValueInLastResponse($attribute, $value, $locale->getCode());
    }

    private function getAdminLocaleCode(): string
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->sharedStorage->get('administrator');

        $response = $this->client->show(Resources::ADMINISTRATORS, (string) $adminUser->getId());

        return $this->responseChecker->getValue($response, 'localeCode');
    }

    private function getFieldValueOfProduct(array $product, string $field): ?string
    {
        if ($field === 'code') {
            return $product['code'];
        }

        if ($field === 'name') {
            return $product['translations'][$this->getAdminLocaleCode()]['name'] ?? null;
        }

        return null;
    }

    private function hasProductImage(Response $response, ProductInterface $product): bool
    {
        $productFromResponse = $this->responseChecker->getResponseContent($response);

        return
            isset($productFromResponse['images'][0]) &&
            str_contains($productFromResponse['images'][0]['path'], $product->getImages()->first()->getPath())
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

    private function getLastResponse(): Response
    {
        return $this->sharedStorage->has('response') ? $this->sharedStorage->get('response') : $this->client->getLastResponse();
    }

    private function getAttributeValueInProperType(
        ProductAttributeInterface $productAttribute,
        string $value,
    ): bool|float|int|string {
        switch ($productAttribute->getStorageType()) {
            case AttributeValueInterface::STORAGE_BOOLEAN:
                return (bool) $value;
            case AttributeValueInterface::STORAGE_FLOAT:
                return (float) $value;
            case AttributeValueInterface::STORAGE_INTEGER:
                return (int) $value;
        }

        return $value;
    }

    private function getSelectAttributeValueUuidByChoiceValue(
        ProductAttributeInterface $attribute,
        string $value,
    ): string {
        $choices = $attribute->getConfiguration()['choices'] ?? [];
        foreach ($choices as $uuid => $choice) {
            if (in_array($value, $choice, true)) {
                return $uuid;
            }
        }

        throw new \InvalidArgumentException(
            sprintf('Value "%s" not found in attribute "%s"', $value, $attribute->getName()),
        );
    }

    private function hasAttributeWithValueInLastResponse(
        ProductAttributeInterface $attribute,
        string $value,
        ?string $localeCode = null,
    ): void {
        $attributeIri = $this->sectionAwareIriConverter->getIriFromResourceInSection($attribute, 'admin');

        $attributes = $this->responseChecker->getValue($this->client->getLastResponse(), 'attributes');
        foreach ($attributes as $attributeValue) {
            if ($attributeValue['attribute'] === $attributeIri && $attributeValue['localeCode'] === $localeCode) {
                $this->assertAttributeValue($value, $attributeValue['value']);

                return;
            }
        }

        throw new \InvalidArgumentException(
            sprintf('The given product does not have attribute %s', $attribute->getName()),
        );
    }

    private function assertAttributeValue(string $expectedValue, $value): void
    {
        if (is_array($value)) {
            Assert::allInArray($value, [$expectedValue]);

            return;
        }

        Assert::same((string) $value, $expectedValue);
    }

    private function assertResponseHasTranslationFieldWithValue(string $field, string $value): void
    {
        Assert::same(
            $this->responseChecker->getTranslationValue($this->client->getLastResponse(), $field),
            $value,
        );
    }
}
