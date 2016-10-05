<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Page\Admin\Product\CreateConfigurableProductPageInterface;
use Sylius\Behat\Page\Admin\Product\CreateSimpleProductPageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateConfigurableProductPageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentProductPageResolverInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingProductsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**x
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
     * @var CurrentProductPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CreateSimpleProductPageInterface $createSimpleProductPage
     * @param CreateConfigurableProductPageInterface $createConfigurableProductPage
     * @param IndexPageInterface $indexPage
     * @param UpdateSimpleProductPageInterface $updateSimpleProductPage
     * @param UpdateConfigurableProductPageInterface $updateConfigurableProductPage
     * @param CurrentProductPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CreateSimpleProductPageInterface $createSimpleProductPage,
        CreateConfigurableProductPageInterface $createConfigurableProductPage,
        IndexPageInterface $indexPage,
        UpdateSimpleProductPageInterface $updateSimpleProductPage,
        UpdateConfigurableProductPageInterface $updateConfigurableProductPage,
        CurrentProductPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->createSimpleProductPage = $createSimpleProductPage;
        $this->createConfigurableProductPage = $createConfigurableProductPage;
        $this->indexPage = $indexPage;
        $this->updateSimpleProductPage = $updateSimpleProductPage;
        $this->updateConfigurableProductPage = $updateConfigurableProductPage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to create a new simple product
     */
    public function iWantToCreateANewSimpleProduct()
    {
        $this->createSimpleProductPage->open();
    }

    /**
     * @Given I want to create a new configurable product
     */
    public function iWantToCreateANewConfigurableProduct()
    {
        $this->createConfigurableProductPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->createSimpleProductPage,
            $this->createConfigurableProductPage,
        ]);

        $currentPage->specifyCode($code);
    }

    /**
     * @When I name it :name in :language
     */
    public function iNameItIn($name, $language)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->createSimpleProductPage,
            $this->createConfigurableProductPage,
        ]);

        $currentPage->nameItIn($name, $language);
    }

    /**
     * @When I rename it to :name in :language
     */
    public function iRenameItToIn($name, $language)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        $currentPage->nameItIn($name, $language);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        /** @var CreatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->createSimpleProductPage,
            $this->createConfigurableProductPage,
        ]);

        Assert::isInstanceOf($currentPage, CreatePageInterface::class);

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
     * @When /^I set its price to ("(?:€|£|\$)[^"]+")$/
     */
    public function iSetItsPriceTo($price)
    {
        $this->createSimpleProductPage->specifyPrice($price);
    }

    /**
     * @Then the product :productName should appear in the shop
     * @Then the product :productName should be in the shop
     * @Then this product should still be named :productName
     */
    public function theProductShouldAppearInTheShop($productName)
    {
        $this->iWantToBrowseProducts();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $productName]),
            sprintf('The product with name %s has not been found.', $productName)
        );
    }

    /**
     * @Given I am browsing products
     * @When I want to browse products
     */
    public function iWantToBrowseProducts()
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should( still) see a product with :field :value
     */
    public function iShouldSeeProductWith($field, $value)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage([$field => $value]),
            sprintf('The product with %s "%s" has not been found.', $field, $value)
        );
    }

    /**
     * @Then I should not see any product with :field :value
     */
    public function iShouldNotSeeAnyProductWith($field, $value)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage([$field => $value]),
            sprintf('The product with %s "%s" has been found.', $field, $value)
        );
    }

    /**
     * @Then the first product on the list should have :field :value
     */
    public function theFirstProductOnTheListShouldHave($field, $value)
    {
        $actualValue = $this->indexPage->getColumnFields($field)[0];

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected first product\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
    }

    /**
     * @When I switch the way products are sorted by :field
     * @When I start sorting products by :field
     * @Given the products are already sorted by :field
     */
    public function iSortProductsBy($field)
    {
        $this->indexPage->sortBy($field);
    }

    /**
     * @Then I should see :numberOfProducts products in the list
     */
    public function iShouldSeeProductsInTheList($numberOfProducts)
    {
        $foundRows = $this->indexPage->countItems();

        Assert::same(
            $numberOfProducts,
            $foundRows,
            '%s rows with products should appear on page, %s rows has been found'
        );
    }

    /**
     * @When I delete the :product product
     * @When I try to delete the :product product
     */
    public function iDeleteProduct(ProductInterface $product)
    {
        $this->sharedStorage->set('product', $product);

        $this->iWantToBrowseProducts();
        $this->indexPage->deleteResourceOnPage(['name' => $product->getName()]);
    }

    /**
     * @Then /^(this product) should not exist in the product catalog$/
     */
    public function productShouldNotExist(ProductInterface $product)
    {
        $this->iWantToBrowseProducts();

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['code' => $product->getCode()]),
            sprintf('Product with code %s exists but should not.', $product->getCode())
        );
    }

    /**
     * @Then I should be notified that this product is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        $this->notificationChecker->checkNotification(
            "Cannot delete, the product is in use.",
            NotificationType::failure()
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
     */
    public function iWantToModifyAProduct(ProductInterface $product)
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
    public function theCodeFieldShouldBeDisabled()
    {
        /** @var UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        Assert::true(
            $currentPage->isCodeDisabled(),
            'Code should be immutable, but it does not.'
        );
    }

    /**
     * @Then /^this product price should be "(?:€|£|\$)([^"]+)"$/
     */
    public function thisProductPriceShouldBeEqualTo($price)
    {
        $this->assertElementValue('price', $price);
    }

    /**
     * @Then this product name should be :name
     */
    public function thisProductElementShouldBe($name)
    {
        $this->assertElementValue('name', $name);
    }

    /**
     * @Then /^I should be notified that (code|name) is required$/
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertValidationMessage($element, sprintf('Please enter product %s.', $element));
    }

    /**
     * @Then I should be notified that price is required
     */
    public function iShouldBeNotifiedThatPriceIsRequired()
    {
        $this->assertValidationMessage('price', 'Please enter the price.');
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        /** @var UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        Assert::isInstanceOf($currentPage, UpdatePageInterface::class);

        $currentPage->saveChanges();
    }

    /**
     * @When /^I change its price to "(?:€|£|\$)([^"]+)"$/
     */
    public function iChangeItsPriceTo($price)
    {
        $this->updateSimpleProductPage->specifyPrice($price);
    }

    /**
     * @Given I add the :optionName option to it
     */
    public function iAddTheOptionToIt($optionName)
    {
        $this->createConfigurableProductPage->selectOption($optionName);
    }

    /**
     * @When I set its :attribute attribute to :value
     */
    public function iSetItsAttributeTo($attribute, $value)
    {
        $this->createSimpleProductPage->addAttribute($attribute, $value);
    }

    /**
     * @When I remove its :attribute attribute
     */
    public function iRemoveItsAttribute($attribute)
    {
        $this->createSimpleProductPage->removeAttribute($attribute);
    }

    /**
     * @Then /^attribute "([^"]+)" of (product "[^"]+") should be "([^"]+)"$/
     */
    public function itsAttributeShouldBe($attribute, ProductInterface $product, $value)
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same(
            $value,
            $this->updateSimpleProductPage->getAttributeValue($attribute),
            sprintf('ProductAttribute "%s" should have value "%s" but it does not.', $attribute, $value)
        );
    }

    /**
     * @Then /^(product "[^"]+") should not have a "([^"]+)" attribute$/
     */
    public function productShouldNotHaveAttribute(ProductInterface $product, $attribute)
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::false(
            $this->updateSimpleProductPage->hasAttribute($attribute),
            sprintf('Product "%s" should not have attribute "%s" but it does.', $product->getName(), $attribute)
        );
    }

    /**
     * @Given product with :element :value should not be added
     */
    public function productWithNameShouldNotBeAdded($element, $value)
    {
        $this->iWantToBrowseProducts();

        Assert::false(
            $this->indexPage->isSingleResourceOnPage([$element => $value]),
            sprintf('Product with %s %s was created, but it should not.', $element, $value)
        );
    }

    /**
     * @When I remove its name from :language translation
     */
    public function iRemoveItsNameFromTranslation($language)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        $currentPage->nameItIn('', $language);
    }

    /**
     * @Then /^this product should have (?:a|an) "([^"]+)" option$/
     */
    public function thisProductShouldHaveOption($productOption)
    {
        $this->updateConfigurableProductPage->isProductOptionChosen($productOption);
    }

    /**
     * @Then the option field should be disabled
     */
    public function theOptionFieldShouldBeDisabled()
    {
        Assert::true(
            $this->updateConfigurableProductPage->isProductOptionsDisabled(),
            'Options field should be immutable, but it does not.'
        );
    }

    /**
     * @When /^I choose main (taxon "([^"]+)")$/
     */
    public function iChooseMainTaxon(TaxonInterface $taxon)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        $currentPage->selectMainTaxon($taxon);
    }

    /**
     * @Then /^(this product) main taxon should be "([^"]+)"$/
     */
    public function thisProductMainTaxonShouldBe(ProductInterface $product, $taxonName)
    {
        /** @var UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        $currentPage->open(['id' => $product->getId()]);

        Assert::true(
            $this->updateConfigurableProductPage->isMainTaxonChosen($taxonName),
            sprintf('The main taxon %s should be chosen, but it does not.', $taxonName)
        );
    }

    /**
     * @Then /^inventory of (this product) should not be tracked$/
     */
    public function thisProductShouldNotBeTracked(ProductInterface $product)
    {
        $this->iWantToModifyAProduct($product);

        Assert::false(
            $this->updateSimpleProductPage->isTracked(),
            '"%s" should not be tracked, but it is.'
        );
    }

    /**
     * @Then /^inventory of (this product) should be tracked$/
     */
    public function thisProductShouldBeTracked(ProductInterface $product)
    {
        $this->iWantToModifyAProduct($product);

        Assert::true(
            $this->updateSimpleProductPage->isTracked(),
            '"%s" should be tracked, but it is not.'
        );
    }

    /**
     * @When I attach the :path image with a code :code
     */
    public function iAttachImageWithACode($path, $code)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->createSimpleProductPage,
            $this->createConfigurableProductPage,
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->has('product') ? $this->sharedStorage->get('product') : null);

        $currentPage->attachImageWithCode($code, $path);
    }

    /**
     * @Then /^(this product) should have(?:| also) an image with a code "([^"]*)"$/
     * @Then /^the (product "[^"]+") should have(?:| also) an image with a code "([^"]*)"$/
     */
    public function thisProductShouldHaveAnImageWithCode(ProductInterface $product, $code)
    {
        $this->sharedStorage->set('product', $product);

        /** @var UpdateSimpleProductPageInterface|UpdateConfigurableProductPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $product);

        Assert::true(
            $currentPage->isImageWithCodeDisplayed($code),
            sprintf('Image with a code %s should have been displayed.', $code)
        );
    }

    /**
     * @Then /^(this product) should not have(?:| also) an image with a code "([^"]*)"$/
     */
    public function thisProductShouldNotHaveAnImageWithCode(ProductInterface $product, $code)
    {
        /** @var UpdateSimpleProductPageInterface|UpdateConfigurableProductPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $product);

        Assert::false(
            $currentPage->isImageWithCodeDisplayed($code),
            sprintf('Image with a code %s should not have been displayed.', $code)
        );
    }

    /**
     * @When I change the image with the :code code to :path
     */
    public function iChangeItsImageToPathForTheCode($path, $code)
    {
        /** @var UpdateSimpleProductPageInterface|UpdateConfigurableProductPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        $currentPage->changeImageWithCode($code, $path);
    }

    /**
     * @When /^I remove(?:| also) an image with a code "([^"]*)"$/
     */
    public function iRemoveAnImageWithACode($code)
    {
        /** @var UpdateSimpleProductPageInterface|UpdateConfigurableProductPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        $currentPage->removeImageWithCode($code);
    }

    /**
     * @When I remove the first image
     */
    public function iRemoveTheFirstImage()
    {
        /** @var UpdateSimpleProductPageInterface|UpdateConfigurableProductPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        $currentPage->removeFirstImage();
    }

    /**
     * @Then this product should not have images
     */
    public function thisProductShouldNotHaveImages()
    {
        /** @var UpdateSimpleProductPageInterface|UpdateConfigurableProductPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        Assert::same(
            0,
            $currentPage->countImages(),
            'This product has %2$s, but it should not have.'
        );
    }

    /**
     * @Then the image code field should be disabled
     */
    public function theImageCodeFieldShouldBeDisabled()
    {
        /** @var UpdateSimpleProductPageInterface|UpdateConfigurableProductPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        Assert::true(
            $currentPage->isImageCodeDisabled(),
            'Image code field should be disabled but it is not.'
        );
    }

    /**
     * @Then I should be notified that the image with this code already exists
     */
    public function iShouldBeNotifiedThatTheImageWithThisCodeAlreadyExists()
    {
        Assert::same($this->updateSimpleProductPage->getValidationMessageForImage('code'), 'Image code must be unique within this product.');
    }

    /**
     * @Then there should still be only one image in the :product product
     */
    public function thereShouldStillBeOnlyOneImageInThisTaxon(ProductInterface $product)
    {
        $this->iWantToModifyAProduct($product);

        /** @var UpdateSimpleProductPageInterface|UpdateConfigurableProductPageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $product);

        Assert::same(
            1,
            $currentPage->countImages(),
            'This product has %2$s images, but it should have only one.'
        );
    }

    /**
     * @param string $element
     * @param string $value
     */
    private function assertElementValue($element, $value)
    {
        /** @var UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $this->sharedStorage->get('product'));

        Assert::isInstanceOf($currentPage, UpdatePageInterface::class);

        Assert::true(
            $currentPage->hasResourceValues(
                [$element => $value]
            ),
            sprintf('Product should have %s with %s value.', $element, $value)
        );
    }

    /**
     * @param string $element
     * @param string $message
     */
    private function assertValidationMessage($element, $message)
    {
        $product = $this->sharedStorage->has('product') ? $this->sharedStorage->get('product') : null;

        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->createSimpleProductPage,
            $this->createConfigurableProductPage,
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $product);

        Assert::same($currentPage->getValidationMessage($element), $message);
    }
}
