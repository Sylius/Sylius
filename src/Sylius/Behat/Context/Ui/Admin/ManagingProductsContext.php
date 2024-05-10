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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Context\Ui\Admin\Helper\ValidationTrait;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Page\Admin\Product\CreateConfigurableProductPageInterface;
use Sylius\Behat\Page\Admin\Product\CreateSimpleProductPageInterface;
use Sylius\Behat\Page\Admin\Product\IndexPageInterface;
use Sylius\Behat\Page\Admin\Product\IndexPerTaxonPageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateConfigurableProductPageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Sylius\Behat\Page\Admin\ProductReview\IndexPageInterface as ProductReviewIndexPageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\CreatePageInterface as VariantCreatePageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\GeneratePageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\UpdatePageInterface as VariantUpdatePageInterface;
use Sylius\Behat\Service\Helper\JavaScriptTestHelperInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Webmozart\Assert\Assert;

final class ManagingProductsContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private CreateSimpleProductPageInterface $createSimpleProductPage,
        private CreateConfigurableProductPageInterface $createConfigurableProductPage,
        private IndexPageInterface $indexPage,
        private UpdateSimpleProductPageInterface $updateSimpleProductPage,
        private UpdateConfigurableProductPageInterface $updateConfigurableProductPage,
        private ProductReviewIndexPageInterface $productReviewIndexPage,
        private IndexPerTaxonPageInterface $indexPerTaxonPage,
        private VariantCreatePageInterface $variantCreatePage,
        private GeneratePageInterface $variantGeneratePage,
        private CurrentPageResolverInterface $currentPageResolver,
        private NotificationCheckerInterface $notificationChecker,
        private VariantUpdatePageInterface $variantUpdatePage,
        private JavaScriptTestHelperInterface $testHelper,
    ) {
    }

    /**
     * @When I want to create a new simple product
     */
    public function iWantToCreateANewSimpleProduct(): void
    {
        $this->testHelper->waitUntilPageOpens($this->createSimpleProductPage);
    }

    /**
     * @When I want to create a new configurable product
     */
    public function iWantToCreateANewConfigurableProduct(): void
    {
        $this->testHelper->waitUntilPageOpens($this->createConfigurableProductPage);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->specifyCode($code ?? '');
    }

    /**
     * @When I do not name it
     * @When I name it :name in :language
     * @When I rename it to :name in :language
     * @When I should be able to name it :name in :language
     */
    public function iRenameItToIn(?string $name = null, ?string $language = null): void
    {
        if ($name !== null && $language !== null) {
            $currentPage = $this->resolveCurrentPage();

            $currentPage->nameItIn($name, $language);
        }
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        /** @var CreatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();

        $currentPage->create();
    }

    /**
     * @When I disable its inventory tracking
     */
    public function iDisableItsTracking()
    {
        $this->updateSimpleProductPage->disableTracking();
    }

    /**
     * @When I enable its inventory tracking
     */
    public function iEnableItsTracking()
    {
        $this->updateSimpleProductPage->enableTracking();
    }

    /**
     * @When /^I set its(?:| default) price to "(?:€|£|\$)([^"]+)" for ("([^"]+)" channel)$/
     */
    public function iSetItsPriceTo(string $price, ChannelInterface $channel)
    {
        $this->createSimpleProductPage->specifyPrice($channel, $price);
    }

    /**
     * @When /^I set its original price to "(?:€|£|\$)([^"]+)" for ("([^"]+)" channel)$/
     */
    public function iSetItsOriginalPriceTo(int $originalPrice, ChannelInterface $channel)
    {
        $this->createSimpleProductPage->specifyOriginalPrice($channel, $originalPrice);
    }

    /**
     * @When I make it available in channel :channel
     */
    public function iMakeItAvailableInChannel(ChannelInterface $channel)
    {
        $this->createSimpleProductPage->checkChannel($channel->getName());
    }

    /**
     * @When I assign it to channel :channel
     */
    public function iAssignItToChannel(ChannelInterface $channel)
    {
        // Temporary solution until we will make current page resolver work with product pages
        $this->updateConfigurableProductPage->checkChannel($channel->getName());
    }

    /**
     * @When I choose :calculatorName calculator
     */
    public function iChooseCalculator($calculatorName)
    {
        $this->createSimpleProductPage->choosePricingCalculator($calculatorName);
    }

    /**
     * @When I set its slug to :slug
     * @When I set its slug to :slug in :language
     * @When I remove its slug
     */
    public function iSetItsSlugToIn(?string $slug = null, $language = 'en_US')
    {
        $this->createSimpleProductPage->specifySlugIn($slug, $language);
    }

    /**
     * @When I choose to show this product in the :channel channel
     */
    public function iChooseToShowThisProductInTheChannel(string $channel): void
    {
        $this->updateSimpleProductPage->showProductInChannel($channel);
    }

    /**
     * @When I choose to show this product in this channel
     */
    public function iChooseToShowThisProductInThisChannel(): void
    {
        $this->updateSimpleProductPage->showProductInSingleChannel();
    }

    /**
     * @When I enable slug modification
     * @When I enable slug modification in :localeCode
     */
    public function iEnableSlugModification($localeCode = 'en_US')
    {
        $this->updateSimpleProductPage->activateLanguageTab($localeCode);
        $this->updateSimpleProductPage->enableSlugModification($localeCode);
    }

    /**
     * @When I choose :channelName as a channel filter
     */
    public function iChooseChannelAsAChannelFilter(string $channelName): void
    {
        $this->indexPage->chooseChannelFilter($channelName);
    }

    /**
     * @When I choose enabled filter
     */
    public function iChooseEnabledFilter(): void
    {
        $this->indexPage->chooseEnabledFilter();
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->indexPage->filter();
    }

    /**
     * @Then I should see the product :productName in the list
     * @Then the product :productName should appear in the store
     * @Then the product :productName should be in the shop
     * @Then this product should still be named :productName
     */
    public function theProductShouldAppearInTheShop(string $productName): void
    {
        $this->iWantToBrowseProducts();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $productName]));
    }

    /**
     * @Given I am browsing products
     * @When I browse products
     * @When I want to browse products
     */
    public function iWantToBrowseProducts()
    {
        $this->indexPage->open();
    }

    /**
     * @When /^I am browsing products from ("([^"]+)" taxon)$/
     */
    public function iAmBrowsingProductsFromTaxon(TaxonInterface $taxon)
    {
        $this->indexPerTaxonPage->open(['taxonId' => $taxon->getId()]);
    }

    /**
     * @When /^I am browsing the (\d+)(?:st|nd|rd|th) page of products from ("([^"]+)" taxon)$/
     * @When /^I go to the (\d+)(?:st|nd|rd|th) page of products from ("([^"]+)" taxon)$/
     */
    public function iAmBrowsingProductsFromTaxonPage(int $page, TaxonInterface $taxon): void
    {
        $this->indexPerTaxonPage->open(['taxonId' => $taxon->getId(), 'page' => $page]);
    }

    /**
     * @When I filter them by :taxonName taxon
     */
    public function iFilterThemByTaxon($taxonName)
    {
        $this->indexPage->filterByTaxon($taxonName);
    }

    /**
     * @When I check (also) the :productName product
     */
    public function iCheckTheProduct(string $productName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $productName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then I should( still) see a product with :field :value
     */
    public function iShouldSeeProductWith($field, $value)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage([$field => $value]));
    }

    /**
     * @Then I should not see any product with :field :value
     */
    public function iShouldNotSeeAnyProductWith($field, $value)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage([$field => $value]));
    }

    /**
     * @Then the first product on the list should have :field :value
     * @Then the first product on the list within this taxon should have :field :value
     */
    public function theFirstProductOnTheListShouldHave($field, $value)
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::same($currentPage->getColumnFields($field)[0], $value);
    }

    /**
     * @Then /^the (\d+)(?:st|nd|rd|th) product on this page should be named "([^"]+)"$/
     */
    public function theNthProductOnThisPageShouldBeNamed(int $position, string $value): void
    {
        $values = $this->indexPerTaxonPage->getColumnFields('name');

        Assert::same($values[$position - 1], $value);

        $this->sharedStorage->set('product_taxon_name', $value);
    }

    /**
     * @Then this product should be at position :position
     */
    public function theNthProductOnThisPageShouldBeAtPosition(int $position): void
    {
        $productName = $this->sharedStorage->get('product_taxon_name');
        Assert::same($this->indexPerTaxonPage->getProductPosition($productName), $position);
    }

    /**
     * @Then the one before last product on the list should have :field :value
     */
    public function theOneBeforeLastProductOnTheListShouldHave(string $field, string $value): void
    {
        $values = $this->indexPerTaxonPage->getColumnFields($field);

        Assert::same($values[count($values) - 2], $value);

        $this->sharedStorage->set('product_taxon_name', $value);
    }

    /**
     * @Then the one before last product on the list should have name :productName with position :position
     */
    public function theOneBeforeLastProductOnTheListShouldHaveNameWithPosition(string $productName, int $position): void
    {
        $productNames = $this->indexPerTaxonPage->getColumnFields('name');

        Assert::same($productNames[count($productNames) - 2], $productName);
        Assert::same($this->indexPerTaxonPage->getProductPosition($productName), $position);

        $this->sharedStorage->set('product_taxon_name', $productName);
    }

    /**
     * @Then the last product on the list should have :field :value
     * @Then the last product on the list within this taxon should have :field :value
     */
    public function theLastProductOnTheListShouldHave($field, $value)
    {
        $values = $this->indexPerTaxonPage->getColumnFields($field);

        Assert::same(end($values), $value);

        $this->sharedStorage->set('product_taxon_name', $value);
    }

    /**
     * @Then the last product on the list should have name :productName with position :position
     */
    public function theLastProductOnTheListShouldHaveNameWithPosition(string $productName, int $position): void
    {
        $productNames = $this->indexPerTaxonPage->getColumnFields('name');

        Assert::same(end($productNames), $productName);
        Assert::same($this->indexPerTaxonPage->getProductPosition($productName), $position);

        $this->sharedStorage->set('product_taxon_name', $productName);
    }

    /**
     * @When I switch the way products are sorted :sortType by :field
     * @When I start sorting products by :field
     * @When the products are already sorted :sortType by :field
     * @When I sort the products :sortType by :field
     */
    public function iSortProductsBy(string $field): void
    {
        $this->indexPage->sortBy($field);
    }

    /**
     * @When I sort this taxon's products :sortType by :field
     */
    public function iSortThisTaxonsProductsBy(string $sortType, string $field): void
    {
        $this->indexPerTaxonPage->sortBy(
            $field,
            str_starts_with($sortType, 'de') ? 'desc' : 'asc',
        );
    }

    /**
     * @Then I should see a single product in the list
     * @Then I should see :numberOfProducts products in the list
     */
    public function iShouldSeeProductsInTheList(int $numberOfProducts = 1): void
    {
        Assert::same($this->indexPage->countItems(), (int) $numberOfProducts);
    }

    /**
     * @Then /^(this product) should not exist in the product catalog$/
     */
    public function productShouldNotExist(ProductInterface $product)
    {
        $this->iWantToBrowseProducts();

        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $product->getCode()]));
    }

    /**
     * @Then I should be notified that this product is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        $this->notificationChecker->checkNotification(
            'Cannot delete, the Product is in use.',
            NotificationType::failure(),
        );
    }

    /**
     * @Then /^(this product) should still exist in the product catalog$/
     */
    public function productShouldExistInTheProductCatalog(ProductInterface $product)
    {
        $this->theProductShouldAppearInTheShop($product->getName());
    }

    /**
     * @When I want to modify the :product product
     * @When /^I want to modify (this product)$/
     * @When /^I want to edit (this product)$/
     * @When I modify the :product product
     */
    public function iWantToModifyAProduct(ProductInterface $product): void
    {
        $this->sharedStorage->set('product', $product);

        if ($product->isSimple()) {
            $this->testHelper->waitUntilPageOpens($this->updateSimpleProductPage, ['id' => $product->getId()]);

            return;
        }

        $this->testHelper->waitUntilPageOpens($this->updateSimpleProductPage, ['id' => $product->getId()]);
    }

    /**
     * @When /^I go to the (\d)(?:st|nd|rd|th) page$/
     */
    public function iGoToPage(int $page): void
    {
        $this->indexPage->goToPage($page);
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::true($currentPage->isCodeDisabled());
    }

    /**
     * @Then the slug field should not be editable
     * @Then the slug field in :localeCode (also )should not be editable
     */
    public function theSlugFieldShouldNotBeEditable($localeCode = 'en_US')
    {
        Assert::true($this->updateSimpleProductPage->isSlugReadonlyIn($localeCode));
    }

    /**
     * @Then this product name should be :name
     */
    public function thisProductElementShouldBe($name)
    {
        $this->assertElementValue('name', $name);
    }

    /**
     * @Then /^I should be notified that (code|name|slug) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertValidationMessage($element, sprintf('Please enter product %s.', $element));
    }

    /**
     * @Then I should be notified that meta keywords are too long
     */
    public function iShouldBeNotifiedThatMetaKeywordsAreTooLong(): void
    {
        $this->assertValidationMessage('meta_keywords', 'Product meta keywords must not be longer than 255 characters.');
    }

    /**
     * @Then I should be notified that meta description is too long
     */
    public function iShouldBeNotifiedThatMetaDescriptionIsTooLong(): void
    {
        $this->assertValidationMessage('meta_description', 'Product meta description must not be longer than 255 characters.');
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     * @When I save my changes to the images
     */
    public function iSaveMyChanges()
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->saveChanges();
    }

    /**
     * @When I cancel my changes
     */
    public function iCancelChanges(): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->cancelChanges();
    }

    /**
     * @When /^I change its price to (?:€|£|\$)([^"]+) for ("([^"]+)" channel)$/
     */
    public function iChangeItsPriceTo(string $price, ChannelInterface $channel)
    {
        $this->updateSimpleProductPage->specifyPrice($channel, $price);
    }

    /**
     * @When /^I change its original price to "(?:€|£|\$)([^"]+)" for ("([^"]+)" channel)$/
     */
    public function iChangeItsOriginalPriceTo(string $price, ChannelInterface $channel)
    {
        $this->updateSimpleProductPage->specifyOriginalPrice($channel, $price);
    }

    /**
     * @Given I add the :optionName option to it
     */
    public function iAddTheOptionToIt($optionName)
    {
        $this->createConfigurableProductPage->selectOption($optionName);
    }

    /**
     * @When I set its :attributeName attribute to :value in :localeCode
     * @When I do not set its :attributeName attribute in :localeCode
     * @When I add the :attributeName attribute
     */
    public function iSetItsAttributeTo(string $attributeName, ?string $value = null, $localeCode = 'en_US'): void
    {
        $this->createSimpleProductPage->addAttribute($attributeName, $value ?? '', $localeCode);
    }

    /**
     * @When I select :value value in :language for the :attribute attribute
     */
    public function iSelectValueInLanguageForTheAttribute(string $value, string $language, string $attribute): void
    {
        $this->createSimpleProductPage->selectAttributeValue($attribute, $value, $language);
    }

    /**
     * @When I select :value value for the :attribute attribute
     */
    public function iSelectValueForTheAttribute(string $value, string $attribute): void
    {
        $this->createSimpleProductPage->selectAttributeValue($attribute, $value, '');
    }

    /**
     * @When I set its non-translatable :attributeName attribute to :value
     */
    public function iSetItsNonTranslatableAttributeTo(string $attributeName, string $value): void
    {
        $this->createSimpleProductPage->addNonTranslatableAttribute($attributeName, $value);
    }

    /**
     * @When I remove its :attribute attribute
     * @When I remove its :attribute attribute from :language
     */
    public function iRemoveItsAttribute($attribute, $language = 'en_US')
    {
        $this->createSimpleProductPage->removeAttribute($attribute, $language);
    }

    /**
     * @When I try to add new attributes
     */
    public function iTryToAddNewAttributes()
    {
        $this->updateSimpleProductPage->addSelectedAttributes();
    }

    /**
     * @When I do not want to have shipping required for this product
     */
    public function iDoNotWantToHaveShippingRequiredForThisProduct()
    {
        $this->createSimpleProductPage->setShippingRequired(false);
    }

    /**
     * @Then attribute :attributeName of product :product should be :value
     * @Then attribute :attributeName of product :product should be :value in :language
     */
    public function itsAttributeShouldBe($attributeName, ProductInterface $product, $value, $language = 'en_US')
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same($this->updateSimpleProductPage->getAttributeValue($attributeName, $language), $value);
    }

    /**
     * @Then select attribute :attributeName of product :product should be :value in :localeCode
     * @Then select attribute :attributeName of product :product should be :value
     */
    public function itsSelectAttributeShouldBeIn(
        string $attributeName,
        ProductInterface $product,
        string $value,
        string $localeCode = '',
    ): void {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same($this->updateSimpleProductPage->getAttributeSelectText($attributeName, $localeCode), $value);
    }

    /**
     * @Then non-translatable attribute :attributeName of product :product should be :value
     */
    public function itsNonTranslatableAttributeShouldBe(string $attributeName, ProductInterface $product, string $value): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same($this->updateSimpleProductPage->getNonTranslatableAttributeValue($attributeName), $value);
    }

    /**
     * @Then /^(product "[^"]+") should not have a "([^"]+)" attribute$/
     */
    public function productShouldNotHaveAttribute(ProductInterface $product, $attribute)
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::false($this->updateSimpleProductPage->hasAttribute($attribute));
    }

    /**
     * @Then /^product "[^"]+" should not have any attributes$/
     * @Then /^product "[^"]+" should have (\d+) attributes?$/
     */
    public function productShouldNotHaveAnyAttributes($count = 0)
    {
        Assert::same($this->updateSimpleProductPage->getNumberOfAttributes(), (int) $count);
    }

    /**
     * @Then product with :element :value should not be added
     */
    public function productWithNameShouldNotBeAdded($element, $value)
    {
        $this->iWantToBrowseProducts();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @When I remove its name from :language translation
     */
    public function iRemoveItsNameFromTranslation($language)
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->nameItIn('', $language);
    }

    /**
     * @When I set its meta keywords to too long string in :language
     */
    public function iSetItsMetaKeywordsToTooLongStringIn(string $language): void
    {
        $this->updateConfigurableProductPage->setMetaKeywords(str_repeat('a', 256), $language);
    }

    /**
     * @When I set its meta description to too long string in :language
     */
    public function iSetItsMetaDescriptionToTooLongStringIn(string $language): void
    {
        $this->updateConfigurableProductPage->setMetaDescription(str_repeat('a', 256), $language);
    }

    /**
     * @When I want to choose main taxon for product :product
     */
    public function iWantToChooseMainTaxonForProduct(ProductInterface $product): void
    {
        $this->iWantToModifyAProduct($product);

        $currentPage = $this->resolveCurrentPage();
        $currentPage->open(['id' => $product->getId()]);
    }

    /**
     * @Then I should be able to choose taxon :taxonName from the list
     */
    public function iShouldBeAbleToChooseTaxonForThisProduct(string $taxonName): void
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::true($currentPage->isTaxonVisibleInMainTaxonList($taxonName));
    }

    /**
     * @Then I should not be able to choose taxon :taxonName from the list
     */
    public function iShouldNotBeAbleToChooseTaxonForThisProduct(string $taxonName): void
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::false($currentPage->isTaxonVisibleInMainTaxonList($taxonName));
    }

    /**
     * @Then /^this product should have (?:a|an) "([^"]+)" option$/
     */
    public function thisProductShouldHaveOption($productOption)
    {
        $this->updateConfigurableProductPage->isProductOptionChosen($productOption);
    }

    /**
     * @Then I should not be able to edit its options
     */
    public function iShouldNotBeAbleToEditItsOptions(): void
    {
        Assert::true($this->updateConfigurableProductPage->isProductOptionsDisabled());
    }

    /**
     * @When /^I choose main (taxon "[^"]+")$/
     * @Then /^I should be able to choose main (taxon "[^"]+")$/
     */
    public function iChooseMainTaxon(TaxonInterface $taxon)
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->selectMainTaxon($taxon);
    }

    /**
     * @Then I should see non-translatable attribute :attribute with value :value%
     */
    public function iShouldSeeNonTranslatableAttributeWithValue(string $attribute, string $value): void
    {
        Assert::true($this->updateSimpleProductPage->hasNonTranslatableAttributeWithValue($attribute, $value));
    }

    /**
     * @Then /^the slug of the ("[^"]+" product) should(?:| still) be "([^"]+)"$/
     * @Then /^the slug of the ("[^"]+" product) should(?:| still) be "([^"]+)" (in the "[^"]+" locale)$/
     */
    public function productSlugShouldBe(ProductInterface $product, $slug, $locale = 'en_US')
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same($this->updateSimpleProductPage->getSlug($locale), $slug);
    }

    /**
     * @Then /^(this product) main taxon should be "([^"]+)"$/
     * @Then /^main taxon of (product "[^"]+") should be "([^"]+)"$/
     */
    public function thisProductMainTaxonShouldBe(ProductInterface $product, $taxonName)
    {
        $currentPage = $this->resolveCurrentPage();
        $currentPage->open(['id' => $product->getId()]);

        Assert::true($currentPage->hasMainTaxonWithName($taxonName));
    }

    /**
     * @Then the product :product should have the :taxon taxon
     */
    public function thisProductTaxonShouldBe(ProductInterface $product, string $taxonName): void
    {
        $currentPage = $this->resolveCurrentPage();
        $currentPage->open(['id' => $product->getId()]);

        Assert::true($currentPage->isTaxonChosen($taxonName));
    }

    /**
     * @Then /^inventory of (this product) should not be tracked$/
     */
    public function thisProductShouldNotBeTracked(ProductInterface $product)
    {
        $this->iWantToModifyAProduct($product);

        Assert::false($this->updateSimpleProductPage->isTracked());
    }

    /**
     * @Then /^inventory of (this product) should be tracked$/
     */
    public function thisProductShouldBeTracked(ProductInterface $product)
    {
        $this->iWantToModifyAProduct($product);

        Assert::true($this->updateSimpleProductPage->isTracked());
    }

    /**
     * @When I attach the :path image with :type type
     * @When I attach the :path image
     * @When I attach the :path image with :type type to this product
     * @When I attach the :path image to this product
     */
    public function iAttachImageWithType(string $path, ?string $type = null): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->attachImage($path, $type);
    }

    /**
     * @When I attach the :path image with selected :productVariant variant to this product
     */
    public function iAttachImageWithSelectedVariantToThisProduct(
        string $path,
        ProductVariantInterface $productVariant,
    ): void {
        $this->updateConfigurableProductPage->attachImage(path: $path, productVariant: $productVariant);
    }

    /**
     * @When I select :productVariant variant for the first image
     */
    public function iSelectVariantForTheFirstImage(ProductVariantInterface $productVariant): void
    {
        $this->updateConfigurableProductPage->selectVariantForFirstImage($productVariant);
    }

    /**
     * @When I associate as :productAssociationType the :productName product
     * @When I associate as :productAssociationType the :firstProductName and :secondProductName products
     * @Then I should be able to associate as :productAssociationType the :productName product
     */
    public function iAssociateProductsAsProductAssociation(
        ProductAssociationTypeInterface $productAssociationType,
        ...$productsNames,
    ) {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->associateProducts($productAssociationType, $productsNames);
    }

    /**
     * @When I remove an associated product :productName from :productAssociationType
     */
    public function iRemoveAnAssociatedProductFromProductAssociation(
        $productName,
        ProductAssociationTypeInterface $productAssociationType,
    ) {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->removeAssociatedProduct($productName, $productAssociationType);
    }

    /**
     * @When I go to the variants list
     */
    public function iGoToTheVariantsList(): void
    {
        $this->resolveCurrentPage()->goToVariantsList();
    }

    /**
     * @When I go to the variant creation page
     */
    public function iGoToTheVariantCreationPage(): void
    {
        $this->resolveCurrentPage()->goToVariantCreation();
    }

    /**
     * @When I go to the variant generation page
     */
    public function iGoToTheVariantGenerationPage(): void
    {
        $this->resolveCurrentPage()->goToVariantGeneration();
    }

    /**
     * @Then /^(?:this product|the product "[^"]+"|it) should(?:| also) have an image with "([^"]*)" type$/
     */
    public function thisProductShouldHaveAnImageWithType(string $type): void
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::true($currentPage->isImageWithTypeDisplayed($type));
    }

    /**
     * @Then its image should have :productVariant variant selected
     */
    public function itsImageShouldHaveVariantSelected(ProductVariantInterface $productVariant): void
    {
        Assert::true($this->updateConfigurableProductPage->hasLastImageAVariant($productVariant));
    }

    /**
     * @Then /^the (product "[^"]+") should still have an accessible image$/
     */
    public function productShouldStillHaveAnAccessibleImage(ProductInterface $product): void
    {
        Assert::true($this->indexPage->hasProductAccessibleImage($product->getCode()));
    }

    /**
     * @Then /^(?:this product|it)(?:| also) should not have any images with "([^"]*)" type$/
     */
    public function thisProductShouldNotHaveAnyImagesWithType($code)
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::false($currentPage->isImageWithTypeDisplayed($code));
    }

    /**
     * @When I change the image with the :type type to :path
     */
    public function iChangeItsImageToPathForTheType($type, $path)
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->changeImageWithType($type, $path);
    }

    /**
     * @When /^I(?:| also) remove an image with "([^"]*)" type$/
     */
    public function iRemoveAnImageWithType($code)
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->removeImageWithType($code);
    }

    /**
     * @When I remove the first image
     */
    public function iRemoveTheFirstImage()
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->removeFirstImage();
    }

    /**
     * @When I change the first image type to :type
     */
    public function iChangeTheFirstImageTypeTo($type)
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->modifyFirstImageType($type);
    }

    /**
     * @Then /^(this product) should not have any images$/
     */
    public function thisProductShouldNotHaveImages(ProductInterface $product)
    {
        $this->iWantToModifyAProduct($product);

        $currentPage = $this->resolveCurrentPage();

        Assert::same($currentPage->countImages(), 0);
    }

    /**
     * @Then /^(this product) should(?:| still) have (?:only one|(\d+)) images?$/
     */
    public function thereShouldStillBeOnlyOneImageInThisProduct(ProductInterface $product, $count = 1)
    {
        $this->iWantToModifyAProduct($product);

        $currentPage = $this->resolveCurrentPage();

        Assert::same($currentPage->countImages(), (int) $count);
    }

    /**
     * @Then /^there should be no reviews of (this product)$/
     */
    public function thereAreNoProductReviews(ProductInterface $product)
    {
        $this->productReviewIndexPage->open();

        Assert::false($this->productReviewIndexPage->isSingleResourceOnPage(['reviewSubject' => $product->getName()]));
    }

    /**
     * @Then this product should( also) have an association :productAssociationType with product :productName
     * @Then this product should( also) have an association :productAssociationType with products :firstProductName and :secondProductName
     */
    public function theProductShouldHaveAnAssociationWithProducts(
        ProductAssociationTypeInterface $productAssociationType,
        ...$productsNames,
    ) {
        foreach ($productsNames as $productName) {
            Assert::true(
                $this->updateSimpleProductPage->hasAssociatedProduct($productName, $productAssociationType),
                sprintf(
                    'This product should have an association %s with product %s.',
                    $productAssociationType->getName(),
                    $productName,
                ),
            );
        }
    }

    /**
     * @Then this product should not have an association :productAssociationType with product :productName
     */
    public function theProductShouldNotHaveAnAssociationWithProduct(
        ProductAssociationTypeInterface $productAssociationType,
        $productName,
    ) {
        Assert::false($this->updateSimpleProductPage->hasAssociatedProduct($productName, $productAssociationType));
    }

    /**
     * @Then I should be notified that original price can not be defined without price
     */
    public function iShouldBeNotifiedThatOriginalPriceCanNotBeDefinedWithoutPrice(): void
    {
        Assert::same(
            $this->createSimpleProductPage->getChannelPricingValidationMessage(),
            'Original price can not be defined without price',
        );
    }

    /**
     * @Then I should be notified that simple product code has to be unique
     */
    public function iShouldBeNotifiedThatSimpleProductCodeHasToBeUnique()
    {
        $this->assertValidationMessage('code', 'Simple product code must be unique among all products and product variants.');
    }

    /**
     * @Then I should be notified that slug has to be unique
     */
    public function iShouldBeNotifiedThatSlugHasToBeUnique()
    {
        $this->assertValidationMessage('slug', 'Product slug must be unique.');
    }

    /**
     * @Then I should be notified that code has to be unique
     */
    public function iShouldBeNotifiedThatCodeHasToBeUnique()
    {
        $this->assertValidationMessage('code', 'Product code must be unique.');
    }

    /**
     * @Then I should be notified that price must be defined for every channel
     */
    public function iShouldBeNotifiedThatPriceMustBeDefinedForEveryChannel()
    {
        Assert::same(
            $this->createSimpleProductPage->getChannelPricingValidationMessage(),
            'You must define price for every enabled channel.',
        );
    }

    /**
     * @Then they should have order like :firstProductName, :secondProductName and :thirdProductName
     */
    public function theyShouldHaveOrderLikeAnd(...$productNames)
    {
        Assert::true($this->indexPerTaxonPage->hasProductsInOrder($productNames));
    }

    /**
     * @When I save my new configuration
     */
    public function iSaveMyNewConfiguration()
    {
        $this->indexPerTaxonPage->savePositions();
    }

    /**
     * @When I set the position of :productName to :position
     */
    public function iSetThePositionOfTo(string $productName, string $position): void
    {
        $this->indexPerTaxonPage->setPositionOfProduct($productName, $position);
    }

    /**
     * @When /^I remove its price from ("[^"]+" channel)$/
     */
    public function iRemoveItsPriceForChannel(ChannelInterface $channel): void
    {
        $this->iSetItsPriceTo('', $channel);
    }

    /**
     * @Then this product should( still) have slug :value in :language (locale)
     */
    public function thisProductElementShouldHaveSlugIn($slug, $language)
    {
        $this->testHelper->waitUntilAssertionPasses(function () use ($language, $slug): void {
            Assert::same($this->updateSimpleProductPage->getSlug($language), $slug);
        });
    }

    /**
     * @When I set its shipping category as :shippingCategoryName
     */
    public function iSetItsShippingCategoryAs($shippingCategoryName)
    {
        $this->createSimpleProductPage->selectShippingCategory($shippingCategoryName);
    }

    /**
     * @Then /^(it|this product) should be priced at (?:€|£|\$)([^"]+) for (channel "([^"]+)")$/
     * @Then /^(product "[^"]+") should be priced at (?:€|£|\$)([^"]+) for (channel "([^"]+)")$/
     */
    public function itShouldBePricedAtForChannel(ProductInterface $product, string $price, ChannelInterface $channel)
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same($this->updateSimpleProductPage->getPriceForChannel($channel), $price);
    }

    /**
     * @Then /^(its|this products) original price should be "(?:€|£|\$)([^"]+)" for (channel "([^"]+)")$/
     */
    public function itsOriginalPriceForChannel(ProductInterface $product, string $originalPrice, ChannelInterface $channel)
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same(
            $this->updateSimpleProductPage->getOriginalPriceForChannel($channel),
            $originalPrice,
        );
    }

    /**
     * @Then /^(this product) should no longer have price for channel "([^"]+)"$/
     */
    public function thisProductShouldNoLongerHavePriceForChannel(ProductInterface $product, $channelName)
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::true(
            $this->updateSimpleProductPage->hasNoPriceForChannel($channelName),
            sprintf('Product "%s" should not have price defined for channel "%s".', $product->getName(), $channelName),
        );
    }

    /**
     * @Then I should be notified that I have to define product variants' prices for newly assigned channels first
     */
    public function iShouldBeNotifiedThatIHaveToDefineProductVariantsPricesForNewlyAssignedChannelsFirst()
    {
        Assert::same(
            $this->updateConfigurableProductPage->getValidationMessage('channels'),
            'You have to define product variants\' prices for newly assigned channels first.',
        );
    }

    /**
     * @Then /^the (product "[^"]+") should not have shipping required$/
     */
    public function theProductWithCodeShouldNotHaveShippingRequired(ProductInterface $product)
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::false($this->updateSimpleProductPage->isShippingRequired());
    }

    /**
     * @Then I should be notified that I have to define the :attribute attribute in :language
     */
    public function iShouldBeNotifiedThatIHaveToDefineTheAttributeIn($attribute, $language)
    {
        Assert::same(
            $this->resolveCurrentPage()->getAttributeValidationErrors($attribute, $language),
            'This value should not be blank.',
        );
    }

    /**
     * @Then I should be notified that the :attribute attribute in :language should be longer than :number
     */
    public function iShouldBeNotifiedThatTheAttributeInShouldBeLongerThan($attribute, $language, $number)
    {
        Assert::same(
            $this->resolveCurrentPage()->getAttributeValidationErrors($attribute, $language),
            sprintf('This value is too short. It should have %s characters or more.', $number),
        );
    }

    /**
     * @Then /^I should be on the variant creation page for (this product)$/
     */
    public function iShouldBeOnTheVariantCreationPageForThisProduct(ProductInterface $product): void
    {
        Assert::true($this->variantCreatePage->isOpen(['productId' => $product->getId()]));
    }

    /**
     * @Then /^I should be on the variant generation page for (this product)$/
     */
    public function iShouldBeOnTheVariantGenerationPageForThisProduct(ProductInterface $product): void
    {
        Assert::true($this->variantGeneratePage->isOpen(['productId' => $product->getId()]));
    }

    /**
     * @Then I should see inventory of this product
     */
    public function iShouldSeeInventoryOfThisProduct(): void
    {
        Assert::true($this->updateSimpleProductPage->hasInventoryTab());
    }

    /**
     * @Then I should not see inventory of this product
     */
    public function iShouldNotSeeInventoryOfThisProduct(): void
    {
        Assert::false($this->updateConfigurableProductPage->hasInventoryTab());
    }

    /**
     * @Then I should be notified that the position :invalidPosition is invalid
     */
    public function iShouldBeNotifiedThatThePositionIsInvalid(string $invalidPosition): void
    {
        $this->notificationChecker->checkNotification(
            sprintf('The position "%s" is invalid.', $invalidPosition),
            NotificationType::failure(),
        );
    }

    /**
     * @Then I should not be able to show this product in shop
     */
    public function iShouldNotBeAbleToShowThisProductInShop(): void
    {
        Assert::true($this->updateSimpleProductPage->isShowInShopButtonDisabled());
    }

    /**
     * @When /^I disable it$/
     */
    public function iDisableIt(): void
    {
        $this->updateSimpleProductPage->disable();
    }

    /**
     * @Then /^(this product) should be disabled along with its variant$/
     */
    public function thisProductShouldBeDisabledAlongWithItsVariant(ProductInterface $product): void
    {
        Assert::true($product->isSimple());
        $this->iWantToModifyAProduct($product);

        Assert::false($this->updateSimpleProductPage->isEnabled());

        $this->variantUpdatePage->open(
            ['productId' => $product->getId(), 'id' => $product->getVariants()->first()->getId()],
        );
        Assert::false($this->variantUpdatePage->isEnabled());
    }

    /**
     * @When /^I enable it$/
     */
    public function iEnableIt(): void
    {
        $this->updateSimpleProductPage->enable();
    }

    /**
     * @Then /^(this product) should be enabled along with its variant$/
     */
    public function thisProductShouldBeEnabledAlongWithItsVariant(ProductInterface $product): void
    {
        Assert::true($product->isSimple());
        $this->iWantToModifyAProduct($product);

        Assert::true($this->updateSimpleProductPage->isEnabled());

        $this->variantUpdatePage->open(
            ['productId' => $product->getId(), 'id' => $product->getVariants()->first()->getId()],
        );
        Assert::true($this->variantUpdatePage->isEnabled());
    }

    /**
     * @Then I should not have configured price for :channel channel
     */
    public function iShouldNotHaveConfiguredPriceForChannel(ChannelInterface $channel): void
    {
        Assert::same($this->updateSimpleProductPage->getPriceForChannel($channel), '');
    }

    /**
     * @Then I should have original price equal to :price in :channel channel
     */
    public function iShouldHaveOriginalPriceEqualInChannel(string $price, ChannelInterface $channel): void
    {
        Assert::contains($price, $this->updateSimpleProductPage->getOriginalPriceForChannel($channel));
    }

    /**
     * @Then the first product on the list shouldn't have a name
     */
    public function theFirstProductOnTheListShouldNotHaveName(): void
    {
        Assert::true($this->indexPage->checkFirstProductHasDataAttribute('data-test-missing-translation-paragraph'));
    }

    /**
     * @Then the last product on the list shouldn't have a name
     */
    public function theLastProductOnTheListShouldNotHaveName(): void
    {
        Assert::true($this->indexPage->checkLastProductHasDataAttribute('data-test-missing-translation-paragraph'));
    }

    /**
     * @Then I should be redirected to the previous page of only enabled products
     */
    public function iShouldBeRedirectedToThePreviousFilteredPageWithFilter(): void
    {
        Assert::true($this->indexPage->isEnabledFilterApplied());
    }

    /**
     * @Then /^I should be redirected to the ([^"]+)(nd) page of only enabled products$/
     */
    public function iShouldBeRedirectedToThePreviousFilteredPageWithFilterAndPage(int $page): void
    {
        Assert::true($this->indexPage->isEnabledFilterApplied());
        Assert::eq($this->indexPage->getPageNumber(), $page);
    }

    /**
     * @Then the show product's page button should be enabled
     */
    public function theShowProductsPageButtonShouldBeEnabled(): void
    {
        Assert::false($this->updateSimpleProductPage->isShowInShopButtonDisabled());
    }

    /**
     * @Then the show product's page button should be disabled
     */
    public function theShowProductsPageButtonShouldBeDisabled(): void
    {
        Assert::true($this->updateSimpleProductPage->isShowInShopButtonDisabled());
    }

    /**
     * @Then /^it should be leading to (the product)'s page in the ("[^"]+" locale)$/
     */
    public function itShouldBeLeadingToTheProductPageInTheLocale(ProductInterface $product, string $localeCode): void
    {
        $productTranslation = $product->getTranslation($localeCode);
        $showProductPageUrl = $this->updateSimpleProductPage->getShowProductInSingleChannelUrl();

        Assert::contains(
            $showProductPageUrl,
            sprintf('/%s/products/%s', $localeCode, $productTranslation->getSlug()),
        );
    }

    /**
     * @param string $element
     * @param string $value
     */
    private function assertElementValue($element, $value)
    {
        /** @var UpdatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();

        Assert::isInstanceOf($currentPage, UpdatePageInterface::class);

        Assert::true(
            $currentPage->hasResourceValues([$element => $value]),
            sprintf('Product should have %s with %s value.', $element, $value),
        );
    }

    /**
     * @param string $element
     * @param string $message
     */
    private function assertValidationMessage($element, $message)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();

        Assert::same($currentPage->getValidationMessage($element), $message);
    }

    private function resolveCurrentPage(): CreateConfigurableProductPageInterface|CreateSimpleProductPageInterface|IndexPerTaxonPageInterface|\Sylius\Behat\Page\Admin\Product\IndexPageInterface|UpdateConfigurableProductPageInterface|UpdateSimpleProductPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->indexPage,
            $this->indexPerTaxonPage,
            $this->createSimpleProductPage,
            $this->createConfigurableProductPage,
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ]);
    }
}
