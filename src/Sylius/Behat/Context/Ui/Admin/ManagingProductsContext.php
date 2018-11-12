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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
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
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Webmozart\Assert\Assert;

final class ManagingProductsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CreateSimpleProductPageInterface
     */
    private $createSimpleProductPage;

    /**
     * @var CreateConfigurableProductPageInterface
     */
    private $createConfigurableProductPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var UpdateSimpleProductPageInterface
     */
    private $updateSimpleProductPage;

    /**
     * @var UpdateConfigurableProductPageInterface
     */
    private $updateConfigurableProductPage;

    /**
     * @var ProductReviewIndexPageInterface
     */
    private $productReviewIndexPage;

    /**
     * @var IndexPerTaxonPageInterface
     */
    private $indexPerTaxonPage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        CreateSimpleProductPageInterface $createSimpleProductPage,
        CreateConfigurableProductPageInterface $createConfigurableProductPage,
        IndexPageInterface $indexPage,
        UpdateSimpleProductPageInterface $updateSimpleProductPage,
        UpdateConfigurableProductPageInterface $updateConfigurableProductPage,
        ProductReviewIndexPageInterface $productReviewIndexPage,
        IndexPerTaxonPageInterface $indexPerTaxonPage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->createSimpleProductPage = $createSimpleProductPage;
        $this->createConfigurableProductPage = $createConfigurableProductPage;
        $this->indexPage = $indexPage;
        $this->updateSimpleProductPage = $updateSimpleProductPage;
        $this->updateConfigurableProductPage = $updateConfigurableProductPage;
        $this->productReviewIndexPage = $productReviewIndexPage;
        $this->indexPerTaxonPage = $indexPerTaxonPage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to create a new simple product
     */
    public function iWantToCreateANewSimpleProduct(): void
    {
        $this->createSimpleProductPage->open();
    }

    /**
     * @Given I want to create a new configurable product
     */
    public function iWantToCreateANewConfigurableProduct(): void
    {
        $this->createConfigurableProductPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->specifyCode($code);
    }

    /**
     * @When I name it :name in :language
     * @When I rename it to :name in :language
     */
    public function iRenameItToIn($name, $language): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->nameItIn($name, $language);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        /** @var CreatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();

        $currentPage->create();
    }

    /**
     * @When I disable its inventory tracking
     */
    public function iDisableItsTracking(): void
    {
        $this->updateSimpleProductPage->disableTracking();
    }

    /**
     * @When I enable its inventory tracking
     */
    public function iEnableItsTracking(): void
    {
        $this->updateSimpleProductPage->enableTracking();
    }

    /**
     * @When /^I set its(?:| default) price to "(?:€|£|\$)([^"]+)" for "([^"]+)" channel$/
     */
    public function iSetItsPriceTo($price, $channelName): void
    {
        $this->createSimpleProductPage->specifyPrice($channelName, $price);
    }

    /**
     * @When /^I set its original price to "(?:€|£|\$)([^"]+)" for "([^"]+)" channel$/
     */
    public function iSetItsOriginalPriceTo($originalPrice, $channelName): void
    {
        $this->createSimpleProductPage->specifyOriginalPrice($channelName, $originalPrice);
    }

    /**
     * @When I make it available in channel :channel
     */
    public function iMakeItAvailableInChannel($channel): void
    {
        $this->createSimpleProductPage->checkChannel($channel);
    }

    /**
     * @When I assign it to channel :channel
     */
    public function iAssignItToChannel($channel): void
    {
        // Temporary solution until we will make current page resolver work with product pages
        $this->updateConfigurableProductPage->checkChannel($channel);
    }

    /**
     * @When I choose :calculatorName calculator
     */
    public function iChooseCalculator($calculatorName): void
    {
        $this->createSimpleProductPage->choosePricingCalculator($calculatorName);
    }

    /**
     * @When I set its slug to :slug
     * @When I set its slug to :slug in :language
     * @When I remove its slug
     */
    public function iSetItsSlugToIn($slug = null, $language = 'en_US'): void
    {
        $this->createSimpleProductPage->specifySlugIn($slug, $language);
    }

    /**
     * @When I enable slug modification
     * @When I enable slug modification in :localeCode
     */
    public function iEnableSlugModification($localeCode = 'en_US'): void
    {
        $this->updateSimpleProductPage->activateLanguageTab($localeCode);
        $this->updateSimpleProductPage->enableSlugModification($localeCode);
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
    public function iWantToBrowseProducts(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When /^I am browsing products from ("([^"]+)" taxon)$/
     */
    public function iAmBrowsingProductsFromTaxon(TaxonInterface $taxon): void
    {
        $this->indexPerTaxonPage->open(['taxonId' => $taxon->getId()]);
    }

    /**
     * @When I filter them by :taxonName taxon
     */
    public function iFilterThemByTaxon($taxonName): void
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
    public function iShouldSeeProductWith($field, $value): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage([$field => $value]));
    }

    /**
     * @Then I should not see any product with :field :value
     */
    public function iShouldNotSeeAnyProductWith($field, $value): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage([$field => $value]));
    }

    /**
     * @Then the first product on the list should have :field :value
     */
    public function theFirstProductOnTheListShouldHave($field, $value): void
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::same($currentPage->getColumnFields($field)[0], $value);
    }

    /**
     * @Then the last product on the list should have :field :value
     */
    public function theLastProductOnTheListShouldHave($field, $value): void
    {
        $values = $this->indexPerTaxonPage->getColumnFields($field);

        Assert::same(end($values), $value);
    }

    /**
     * @When I switch the way products are sorted by :field
     * @When I start sorting products by :field
     * @Given the products are already sorted by :field
     */
    public function iSortProductsBy($field): void
    {
        $this->indexPage->sortBy($field);
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
     * @When I delete the :product product
     * @When I try to delete the :product product
     */
    public function iDeleteProduct(ProductInterface $product): void
    {
        $this->sharedStorage->set('product', $product);

        $this->iWantToBrowseProducts();
        $this->indexPage->deleteResourceOnPage(['name' => $product->getName()]);
    }

    /**
     * @Then /^(this product) should not exist in the product catalog$/
     */
    public function productShouldNotExist(ProductInterface $product): void
    {
        $this->iWantToBrowseProducts();

        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $product->getCode()]));
    }

    /**
     * @Then I should be notified that this product is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure(): void
    {
        $this->notificationChecker->checkNotification(
            'Cannot delete, the product is in use.',
            NotificationType::failure()
        );
    }

    /**
     * @Then /^(this product) should still exist in the product catalog$/
     */
    public function productShouldExistInTheProductCatalog(ProductInterface $product): void
    {
        $this->theProductShouldAppearInTheShop($product->getName());
    }

    /**
     * @When I want to modify the :product product
     * @When /^I want to modify (this product)$/
     */
    public function iWantToModifyAProduct(ProductInterface $product): void
    {
        $this->sharedStorage->set('product', $product);

        if ($product->isSimple()) {
            $this->updateSimpleProductPage->open(['id' => $product->getId()]);

            return;
        }

        $this->updateConfigurableProductPage->open(['id' => $product->getId()]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::true($currentPage->isCodeDisabled());
    }

    /**
     * @Then the slug field should not be editable
     * @Then the slug field in :localeCode (also )should not be editable
     */
    public function theSlugFieldShouldNotBeEditable($localeCode = 'en_US'): void
    {
        Assert::true($this->updateSimpleProductPage->isSlugReadonlyIn($localeCode));
    }

    /**
     * @Then this product name should be :name
     */
    public function thisProductElementShouldBe($name): void
    {
        $this->assertElementValue('name', $name);
    }

    /**
     * @Then /^I should be notified that (code|name|slug) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired($element): void
    {
        $this->assertValidationMessage($element, sprintf('Please enter product %s.', $element));
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->saveChanges();
    }

    /**
     * @When /^I change its price to (?:€|£|\$)([^"]+) for "([^"]+)" channel$/
     */
    public function iChangeItsPriceTo($price, $channelName): void
    {
        $this->updateSimpleProductPage->specifyPrice($channelName, $price);
    }

    /**
     * @When /^I change its original price to "(?:€|£|\$)([^"]+)" for "([^"]+)" channel$/
     */
    public function iChangeItsOriginalPriceTo($price, $channelName): void
    {
        $this->updateSimpleProductPage->specifyOriginalPrice($channelName, $price);
    }

    /**
     * @Given I add the :optionName option to it
     */
    public function iAddTheOptionToIt($optionName): void
    {
        $this->createConfigurableProductPage->selectOption($optionName);
    }

    /**
     * @When I set its :attribute attribute to :value
     * @When I set its :attribute attribute to :value in :language
     * @When I do not set its :attribute attribute in :language
     */
    public function iSetItsAttributeTo($attribute, $value = null, $language = 'en_US'): void
    {
        $this->createSimpleProductPage->addAttribute($attribute, $value, $language);
    }

    /**
     * @When I remove its :attribute attribute
     * @When I remove its :attribute attribute from :language
     */
    public function iRemoveItsAttribute($attribute, $language = 'en_US'): void
    {
        $this->createSimpleProductPage->removeAttribute($attribute, $language);
    }

    /**
     * @When I try to add new attributes
     */
    public function iTryToAddNewAttributes(): void
    {
        $this->updateSimpleProductPage->addSelectedAttributes();
    }

    /**
     * @When I do not want to have shipping required for this product
     */
    public function iDoNotWantToHaveShippingRequiredForThisProduct(): void
    {
        $this->createSimpleProductPage->setShippingRequired(false);
    }

    /**
     * @Then attribute :attributeName of product :product should be :value
     * @Then attribute :attributeName of product :product should be :value in :language
     */
    public function itsAttributeShouldBe($attributeName, ProductInterface $product, $value, $language = 'en_US'): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same($this->updateSimpleProductPage->getAttributeValue($attributeName, $language), $value);
    }

    /**
     * @Then /^(product "[^"]+") should not have a "([^"]+)" attribute$/
     */
    public function productShouldNotHaveAttribute(ProductInterface $product, $attribute): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::false($this->updateSimpleProductPage->hasAttribute($attribute));
    }

    /**
     * @Then /^product "[^"]+" should not have any attributes$/
     * @Then /^product "[^"]+" should have (\d+) attributes?$/
     */
    public function productShouldNotHaveAnyAttributes($count = 0): void
    {
        Assert::same($this->updateSimpleProductPage->getNumberOfAttributes(), (int) $count);
    }

    /**
     * @Given product with :element :value should not be added
     */
    public function productWithNameShouldNotBeAdded($element, $value): void
    {
        $this->iWantToBrowseProducts();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @When I remove its name from :language translation
     */
    public function iRemoveItsNameFromTranslation($language): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->nameItIn('', $language);
    }

    /**
     * @Then /^this product should have (?:a|an) "([^"]+)" option$/
     */
    public function thisProductShouldHaveOption($productOption): void
    {
        $this->updateConfigurableProductPage->isProductOptionChosen($productOption);
    }

    /**
     * @Then the option field should be disabled
     */
    public function theOptionFieldShouldBeDisabled(): void
    {
        Assert::true($this->updateConfigurableProductPage->isProductOptionsDisabled());
    }

    /**
     * @When /^I choose main (taxon "[^"]+")$/
     */
    public function iChooseMainTaxon(TaxonInterface $taxon): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->selectMainTaxon($taxon);
    }

    /**
     * @Then /^the slug of the ("[^"]+" product) should(?:| still) be "([^"]+)"$/
     * @Then /^the slug of the ("[^"]+" product) should(?:| still) be "([^"]+)" (in the "[^"]+" locale)$/
     */
    public function productSlugShouldBe(ProductInterface $product, $slug, $locale = 'en_US'): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same($this->updateSimpleProductPage->getSlug($locale), $slug);
    }

    /**
     * @Then /^(this product) main taxon should be "([^"]+)"$/
     */
    public function thisProductMainTaxonShouldBe(ProductInterface $product, $taxonName): void
    {
        $currentPage = $this->resolveCurrentPage();
        $currentPage->open(['id' => $product->getId()]);

        Assert::true($currentPage->isMainTaxonChosen($taxonName));
    }

    /**
     * @Then /^inventory of (this product) should not be tracked$/
     */
    public function thisProductShouldNotBeTracked(ProductInterface $product): void
    {
        $this->iWantToModifyAProduct($product);

        Assert::false($this->updateSimpleProductPage->isTracked());
    }

    /**
     * @Then /^inventory of (this product) should be tracked$/
     */
    public function thisProductShouldBeTracked(ProductInterface $product): void
    {
        $this->iWantToModifyAProduct($product);

        Assert::true($this->updateSimpleProductPage->isTracked());
    }

    /**
     * @When I attach the :path image with :type type
     * @When I attach the :path image
     */
    public function iAttachImageWithType($path, $type = null): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->attachImage($path, $type);
    }

    /**
     * @When I associate as :productAssociationType the :productName product
     * @When I associate as :productAssociationType the :firstProductName and :secondProductName products
     */
    public function iAssociateProductsAsProductAssociation(
        ProductAssociationTypeInterface $productAssociationType,
        ...$productsNames
    ): void {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->associateProducts($productAssociationType, $productsNames);
    }

    /**
     * @When I remove an associated product :productName from :productAssociationType
     */
    public function iRemoveAnAssociatedProductFromProductAssociation(
        $productName,
        ProductAssociationTypeInterface $productAssociationType
    ): void {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->removeAssociatedProduct($productName, $productAssociationType);
    }

    /**
     * @Then /^(?:this product|the product "[^"]+"|it) should(?:| also) have an image with "([^"]*)" type$/
     */
    public function thisProductShouldHaveAnImageWithType($type): void
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::true($currentPage->isImageWithTypeDisplayed($type));
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
    public function thisProductShouldNotHaveAnyImagesWithType($code): void
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::false($currentPage->isImageWithTypeDisplayed($code));
    }

    /**
     * @When I change the image with the :type type to :path
     */
    public function iChangeItsImageToPathForTheType($type, $path): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->changeImageWithType($type, $path);
    }

    /**
     * @When /^I(?:| also) remove an image with "([^"]*)" type$/
     */
    public function iRemoveAnImageWithType($code): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->removeImageWithType($code);
    }

    /**
     * @When I remove the first image
     */
    public function iRemoveTheFirstImage(): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->removeFirstImage();
    }

    /**
     * @When I change the first image type to :type
     */
    public function iChangeTheFirstImageTypeTo($type): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->modifyFirstImageType($type);
    }

    /**
     * @Then /^(this product) should not have any images$/
     */
    public function thisProductShouldNotHaveImages(ProductInterface $product): void
    {
        $this->iWantToModifyAProduct($product);

        $currentPage = $this->resolveCurrentPage();

        Assert::same($currentPage->countImages(), 0);
    }

    /**
     * @Then /^(this product) should(?:| still) have (?:only one|(\d+)) images?$/
     */
    public function thereShouldStillBeOnlyOneImageInThisProduct(ProductInterface $product, $count = 1): void
    {
        $this->iWantToModifyAProduct($product);

        $currentPage = $this->resolveCurrentPage();

        Assert::same($currentPage->countImages(), (int) $count);
    }

    /**
     * @Then /^there should be no reviews of (this product)$/
     */
    public function thereAreNoProductReviews(ProductInterface $product): void
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
        ...$productsNames
    ): void {
        foreach ($productsNames as $productName) {
            Assert::true(
                $this->updateSimpleProductPage->hasAssociatedProduct($productName, $productAssociationType),
                sprintf(
                    'This product should have an association %s with product %s.',
                    $productAssociationType->getName(),
                    $productName
                )
            );
        }
    }

    /**
     * @Then this product should not have an association :productAssociationType with product :productName
     */
    public function theProductShouldNotHaveAnAssociationWithProduct(
        ProductAssociationTypeInterface $productAssociationType,
        $productName
    ): void {
        Assert::false($this->updateSimpleProductPage->hasAssociatedProduct($productName, $productAssociationType));
    }

    /**
     * @Then I should be notified that simple product code has to be unique
     */
    public function iShouldBeNotifiedThatSimpleProductCodeHasToBeUnique(): void
    {
        $this->assertValidationMessage('code', 'Simple product code must be unique among all products and product variants.');
    }

    /**
     * @Then I should be notified that slug has to be unique
     */
    public function iShouldBeNotifiedThatSlugHasToBeUnique(): void
    {
        $this->assertValidationMessage('slug', 'Product slug must be unique.');
    }

    /**
     * @Then I should be notified that code has to be unique
     */
    public function iShouldBeNotifiedThatCodeHasToBeUnique(): void
    {
        $this->assertValidationMessage('code', 'Product code must be unique.');
    }

    /**
     * @Then I should be notified that price must be defined for every channel
     */
    public function iShouldBeNotifiedThatPriceMustBeDefinedForEveryChannel(): void
    {
        $this->assertValidationMessage('channel_pricings', 'You must define price for every channel.');
    }

    /**
     * @Then they should have order like :firstProductName, :secondProductName and :thirdProductName
     */
    public function theyShouldHaveOrderLikeAnd(...$productNames): void
    {
        Assert::true($this->indexPerTaxonPage->hasProductsInOrder($productNames));
    }

    /**
     * @When I save my new configuration
     */
    public function iSaveMyNewConfiguration(): void
    {
        $this->indexPerTaxonPage->savePositions();
    }

    /**
     * @When I set the position of :productName to :position
     */
    public function iSetThePositionOfTo($productName, $position): void
    {
        $this->indexPerTaxonPage->setPositionOfProduct($productName, (int) $position);
    }

    /**
     * @Then this product should( still) have slug :value in :language
     */
    public function thisProductElementShouldHaveSlugIn($slug, $language): void
    {
        Assert::same($this->updateSimpleProductPage->getSlug($language), $slug);
    }

    /**
     * @When I set its shipping category as :shippingCategoryName
     */
    public function iSetItsShippingCategoryAs($shippingCategoryName): void
    {
        $this->createSimpleProductPage->selectShippingCategory($shippingCategoryName);
    }

    /**
     * @Then /^(it|this product) should be priced at (?:€|£|\$)([^"]+) for channel "([^"]+)"$/
     * @Then /^(product "[^"]+") should be priced at (?:€|£|\$)([^"]+) for channel "([^"]+)"$/
     */
    public function itShouldBePricedAtForChannel(ProductInterface $product, $price, $channelName): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same($this->updateSimpleProductPage->getPriceForChannel($channelName), $price);
    }

    /**
     * @Then /^(its|this products) original price should be "(?:€|£|\$)([^"]+)" for channel "([^"]+)"$/
     */
    public function itsOriginalPriceForChannel(ProductInterface $product, $originalPrice, $channelName): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same(
            $this->updateSimpleProductPage->getOriginalPriceForChannel($channelName),
            $originalPrice
        );
    }

    /**
     * @Then /^(this product) should no longer have price for channel "([^"]+)"$/
     */
    public function thisProductShouldNoLongerHavePriceForChannel(ProductInterface $product, $channelName): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        try {
            $this->updateSimpleProductPage->getPriceForChannel($channelName);
        } catch (ElementNotFoundException $exception) {
            return;
        }

        throw new \Exception(
            sprintf('Product "%s" should not have price defined for channel "%s".', $product->getName(), $channelName)
        );
    }

    /**
     * @Then I should be notified that I have to define product variants' prices for newly assigned channels first
     */
    public function iShouldBeNotifiedThatIHaveToDefineProductVariantsPricesForNewlyAssignedChannelsFirst(): void
    {
        Assert::same(
            $this->updateConfigurableProductPage->getValidationMessage('channels'),
            'You have to define product variants\' prices for newly assigned channels first.'
        );
    }

    /**
     * @Then /^the (product "[^"]+") should not have shipping required$/
     */
    public function theProductWithCodeShouldNotHaveShippingRequired(ProductInterface $product): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::false($this->updateSimpleProductPage->isShippingRequired());
    }

    /**
     * @Then I should be notified that I have to define the :attribute attribute in :language
     */
    public function iShouldBeNotifiedThatIHaveToDefineTheAttributeIn($attribute, $language): void
    {
        Assert::same(
            $this->resolveCurrentPage()->getAttributeValidationErrors($attribute, $language),
            'This value should not be blank.'
        );
    }

    /**
     * @Then I should be notified that the :attribute attribute in :language should be longer than :number
     */
    public function iShouldBeNotifiedThatTheAttributeInShouldBeLongerThan($attribute, $language, $number): void
    {
        Assert::same(
            $this->resolveCurrentPage()->getAttributeValidationErrors($attribute, $language),
            sprintf('This value is too short. It should have %s characters or more.', $number)
        );
    }

    private function assertElementValue(string $element, string $value): void
    {
        /** @var UpdatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();

        Assert::isInstanceOf($currentPage, UpdatePageInterface::class);

        Assert::true(
            $currentPage->hasResourceValues([$element => $value]),
            sprintf('Product should have %s with %s value.', $element, $value)
        );
    }

    private function assertValidationMessage(string $element, string $message): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();

        Assert::same($currentPage->getValidationMessage($element), $message);
    }

    /**
     * @return IndexPageInterface|IndexPerTaxonPageInterface|CreateSimpleProductPageInterface|CreateConfigurableProductPageInterface|UpdateSimpleProductPageInterface|UpdateConfigurableProductPageInterface
     */
    private function resolveCurrentPage()
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
