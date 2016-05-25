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
     * @Given /^I want to create a new variant of (this product)$/
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
        $this->updatePage->nameIt($name);
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
     * @When /^I set its price to ("(?:€|£|\$)[^"]+")$/
     */
    public function iSetItsPriceTo($price)
    {
        $this->createPage->specifyPrice($price);
    }

    /**
     * @Then the :productVariantName variant of the :product product should appear in the shop
     */
    public function theProductVariantShouldAppearInTheShop($productVariantName, ProductInterface $product)
    {
        $this->iWantToViewAllVariantsOfThisProduct($product);

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['presentation' => $productVariantName]),
            sprintf('The product variant with name %s has not been found.', $productVariantName)
        );
    }

    /**
     * @When /^I want to view all variants of (this product)$/
     */
    public function iWantToViewAllVariantsOfThisProduct(ProductInterface $product)
    {
        $this->indexPage->open(['productId' => $product->getId()]);
    }

    /**
     * @Then I should see :numberOfProductVariants variants in the list
     * @Then I should see :numberOfProductVariants variant in the list
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
     * @When /^I delete the ("[^"]+" variant of product "[^"]+")$/
     * @When /^I try to delete the ("[^"]+" variant of product "[^"]+")$/
     */
    public function iDeleteTheVariantOfProduct(ProductVariantInterface $productVariant)
    {
        $this->iWantToViewAllVariantsOfThisProduct($productVariant->getProduct());

        $this->indexPage->deleteResourceOnPage(['code' => $productVariant->getCode()]);
    }

    /**
     * @Then /^(this variant) should not exist in the product catalog$/
     */
    public function productVariantShouldNotExist(ProductVariantInterface $productVariant)
    {
        $this->iWantToViewAllVariantsOfThisProduct($productVariant->getProduct());

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['presentation' => $productVariant->getPresentation()]),
            sprintf('Product variant with code %s exists but should not.', $productVariant->getPresentation())
        );
    }

    /**
     * @Then I should be notified that this variant is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        $this->notificationChecker->checkNotification(
            "Cannot delete, the product variant is in use.",
            NotificationType::failure()
        );
    }

    /**
     * @Then /^(this variant) should still exist in the product catalog$/
     */
    public function productShouldExistInTheProductCatalog(ProductVariantInterface $productVariant)
    {
        $this->theProductVariantShouldAppearInTheShop($productVariant->getPresentation(), $productVariant->getProduct());
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
