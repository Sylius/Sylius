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
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\CreatePageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingProductVariantsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given /^I want to create a new product variant for (this product)$/
     */
    public function iWantToCreateANewProduct(ProductInterface $product)
    {
        $this->createPage->open(['productId' => $product->getId()]);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I name it :name
     */
    public function iNameItIn($name)
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @When I rename it to :name
     */
    public function iRenameItTo($name)
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @When /^I specify its price to ("(?:€|£|\$)[^"]+")$/
     */
    public function iSpecifyItsPriceTo($price)
    {
        $this->createPage->specifyPrice($price);
    }

    /**
     * @Given the product variant :productVariantName should appear in the shop
     * @Given the product variant :productVariantName should be in the shop
     * @Given this product variant should still be named :productVariantName
     */
    public function theProductVariantShouldAppearInTheShop($productVariantName)
    {
        $this->iWantToBrowseProducts();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $productVariantName]),
            sprintf('The product variant with name %s has not been found.', $productVariantName)
        );
    }

    /**
     * @When I want to browse products
     */
    public function iWantToBrowseProducts()
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see :numberOfProductVariants products in the list
     */
    public function iShouldSeeProductsInTheList($numberOfProductVariants)
    {
        $foundRows = $this->indexPage->countItems();

        Assert::same(
            $numberOfProductVariants,
            $foundRows,
            '%s rows with product variants should appear on page, %s rows has been found'
        );
    }

    /**
     * @When I delete the :productVariant productVariant
     * @When I try to delete the :productVariant productVariant
     */
    public function iDeleteProductVariant(ProductVariantInterface $productVariant)
    {
        $this->sharedStorage->set('product_variant', $productVariant);

        $this->iWantToBrowseProducts();
        $this->indexPage->deleteResourceOnPage(['name' => $productVariant->getName()]);
    }

    /**
     * @Then /^(this product variant) should not exist in the product catalog$/
     */
    public function productVariantShouldNotExist(ProductVariantInterface $product)
    {
        $this->iWantToBrowseProducts();

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['presentation' => $product->getPresentation()]),
            sprintf('Product variant with code %s exists but should not.', $product->getPresentation())
        );
    }

    /**
     * @Then I should be notified that this product is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        $this->notificationChecker->checkNotification(
            "Cannot delete, the product variant is in use.",
            NotificationType::failure()
        );
    }

    /**
     * @Then /^(this product variant) should still exist in the product catalog$/
     */
    public function productShouldExistInTheProductCatalog(ProductVariantInterface $productVariant)
    {
        $this->theProductShouldAppearInTheShop($productVariant->getPresentation());
    }

    /**
     * @When I want to modify the :productVariant product variant
     * @When /^I want to modify (this product variant)$/
     */
    public function iWantToModifyAProduct(ProductVariantInterface $productVariant)
    {
        $this->updatePage->open(['id' => $productVariant->getId()]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->updatePage->isCodeDisabled(),
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
        $this->assertElementValue('presentation', $name);
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
        $this->updatePage->saveChanges();
    }

    /**
     * @When /^I change its price to "(?:€|£|\$)([^"]+)"$/
     */
    public function iChangeItsPriceTo($price)
    {
        $this->updatePage->specifyPrice($price);
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
     * @When I remove its name
     */
    public function iRemoveItsNameFromTranslation()
    {
        $this->updatePage->nameIt('');
    }

    /**
     * @param string $element
     * @param string $value
     */
    private function assertElementValue($element, $value)
    {
        Assert::true(
            $this->updatePage->hasResourceValues(
                [$element => $value]
            ),
            sprintf('Product should have %s with %s value.', $element, $value)
        );
    }

    /**
     * @param string $element
     */
    private function assertValidationMessage($element, $message)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->createPage,
            $this->updatePage,
        ]);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, $message),
            sprintf('Product %s should be required.', $element)
        );
    }
}
