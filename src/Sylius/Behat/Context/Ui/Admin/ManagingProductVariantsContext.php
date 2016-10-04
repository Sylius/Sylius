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
use Sylius\Behat\Page\Admin\ProductVariant\CreatePageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\IndexPageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Behat\Service\SharedStorageInterface;
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
     * @When I disable its inventory tracking
     */
    public function iDisableItsTracking()
    {
        $this->updatePage->disableTracking();
    }

    /**
     * @When I enable its inventory tracking
     */
    public function iEnableItsTracking()
    {
        $this->updatePage->enableTracking();
    }

    /**
     * @When /^I set its price to ("(?:€|£|\$)[^"]+")$/
     */
    public function iSetItsPriceTo($price)
    {
        $this->createPage->specifyPrice($price);
    }

    /**
     * @Then the :productVariantCode variant of the :product product should appear in the shop
     */
    public function theProductVariantShouldAppearInTheShop($productVariantCode, ProductInterface $product)
    {
        $this->iWantToViewAllVariantsOfThisProduct($product);

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['code' => $productVariantCode]),
            sprintf('The product variant with code %s has not been found.', $productVariantCode)
        );
    }

    /**
     * @When /^I (?:|want to )view all variants of (this product)$/
     * @When /^I view(?:| all) variants of the (product "[^"]+")$/
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
            $this->indexPage->isSingleResourceOnPage(['name' => $productVariant->getName()]),
            sprintf('Product variant with code %s exists but should not.', $productVariant->getName())
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
        $this->theProductVariantShouldAppearInTheShop($productVariant->getCode(), $productVariant->getProduct());
    }

    /**
     * @When /^I want to modify the ("[^"]+" product variant)$/
     * @When /^I want to modify (this product variant)$/
     */
    public function iWantToModifyAProduct(ProductVariantInterface $productVariant)
    {
        $this->updatePage->open(['id' => $productVariant->getId(), 'productId' => $productVariant->getProduct()->getId()]);
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
     * @Then /^the variant "([^"]+)" should have (\d+) items on hand$/
     */
    public function thisVariantShouldHaveItemsOnHand($productVariantName, $quantity)
    {
        Assert::true(
            $this->indexPage->isSingleResourceWithSpecificElementOnPage(['name' => $productVariantName], sprintf('td > div.ui.label:contains("%s")', $quantity)),
            sprintf('The product variant %s should have %s items on hand, but it does not.',$productVariantName, $quantity)
        );
    }

    /**
     * @Then /^the "([^"]+)" variant of ("[^"]+" product) should have (\d+) items on hand$/
     */
    public function theVariantOfProductShouldHaveItemsOnHand($productVariantName, ProductInterface $product, $quantity)
    {
        $this->indexPage->open(['productId' => $product->getId()]);

        Assert::true(
            $this->indexPage->isSingleResourceWithSpecificElementOnPage(['name' => $productVariantName], sprintf('td > div.ui.label:contains("%s")', $quantity)),
            sprintf('The product variant %s should have %s items on hand, but it does not.',$productVariantName, $quantity)
        );
    }

    /**
     * @Then /^inventory of (this variant) should not be tracked$/
     */
    public function thisProductVariantShouldNotBeTracked(ProductVariantInterface $productVariant)
    {
        $this->iWantToModifyAProduct($productVariant);

        Assert::false(
            $this->updatePage->isTracked(),
            'This variant should not be tracked, but it is.'
        );
    }

    /**
     * @Then /^inventory of (this variant) should be tracked$/
     */
    public function thisProductVariantShouldBeTracked(ProductVariantInterface $productVariant)
    {
        $this->iWantToModifyAProduct($productVariant);

        Assert::true(
            $this->updatePage->isTracked(),
            'This variant should be tracked, but it is not.'
        );
    }

    /**
     * @Then /^I should see that the ("([^"]+)" variant) is not tracked$/
     */
    public function iShouldSeeThatIsNotTracked(ProductVariantInterface $productVariant)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $productVariant->getName(), 'inventory' => 'Not tracked']),
            sprintf('This "%s" variant should have label not tracked, but it does not have', $productVariant->getName())
        );
    }

    /**
     * @Then /^I should see that the ("[^"]+" variant) has zero on hand quantity$/
     */
    public function iShouldSeeThatTheVariantHasZeroOnHandQuantity(ProductVariantInterface $productVariant)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $productVariant->getName(), 'inventory' => '0 Available on hand']),
            sprintf('This "%s" variant should have 0 on hand quantity, but it does not.', $productVariant->getName())
        );
    }

    /**
     * @Then /^(\d+) units of (this product) should be on hold$/
     */
    public function unitsOfThisProductShouldBeOnHold($quantity, ProductInterface $product)
    {
        Assert::eq(
            $quantity,
            $this->indexPage->getOnHoldQuantityFor($product->getFirstVariant()),
            sprintf(
                'Unexpected on hold quantity for "%s" variant. It should be "%s" but is "%s"',
                $product->getFirstVariant()->getName(),
                $quantity,
                $this->indexPage->getOnHoldQuantityFor($product->getFirstVariant())
            )
        );
    }

    /**
     * @Then /^(\d+) units of (this product) should be on hand$/
     */
    public function unitsOfThisProductShouldBeOnHand($quantity, ProductInterface $product)
    {
        Assert::eq(
            $quantity,
            $this->indexPage->getOnHandQuantityFor($product->getFirstVariant()),
            sprintf(
                'Unexpected on hand quantity for "%s" variant. It should be "%s" but is "%s"',
                $product->getFirstVariant()->getName(),
                $quantity,
                $this->indexPage->getOnHandQuantityFor($product->getFirstVariant())
            )
        );
    }

    /**
     * @Then /^there should be no units of (this product) on hold$/
     */
    public function thereShouldBeNoUnitsOfThisProductOnHold(ProductInterface $product)
    {
        Assert::eq(
            0,
            $this->indexPage->getOnHoldQuantityFor($product->getFirstVariant()),
            sprintf(
                'Unexpected on hand quantity for "%s" variant. It should be "%s" but is "%s"',
                $product->getFirstVariant()->getName(),
                0,
                $this->indexPage->getOnHandQuantityFor($product->getFirstVariant())
            )
        );
    }

    /**
     * @Then the :variant variant should have :amount items on hold
     * @Then /^(this variant) should have (\d+) items on hold$/
     */
    public function thisVariantShouldHaveItemsOnHold(ProductVariantInterface $variant, $amount)
    {
        Assert::same(
            $amount,
            $this->indexPage->getOnHoldQuantityFor($variant),
            sprintf(
                'Unexpected on hold quantity for "%s" variant. It should be "%s" but is "%s"',
                $variant->getName(),
                $amount,
                $this->indexPage->getOnHandQuantityFor($variant)
            )
        );
    }

    /**
     * @Then the :variant variant of :product product should have :amount items on hold
     */
    public function theVariantOfProductShouldHaveItemsOnHold(ProductVariantInterface $variant, ProductInterface $product, $amount)
    {
        $this->indexPage->open(['productId' => $product->getId()]);

        Assert::same(
            $amount,
            $this->indexPage->getOnHoldQuantityFor($variant),
            sprintf(
                'Unexpected on hold quantity for "%s" variant. It should be "%s" but is "%s"',
                $variant->getName(),
                $amount,
                $this->indexPage->getOnHandQuantityFor($variant)
            )
        );
    }

    /**
     * @When I set its :optionName option to :optionValue
     */
    public function iSetItsOptionAs($optionName, $optionValue)
    {
        $this->createPage->selectOption($optionName, $optionValue);
    }

    /**
     * @param string $element
     * @param $message
     */
    private function assertValidationMessage($element, $message)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $message);
    }
}
