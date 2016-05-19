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
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
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
     * @When /^I set its price to ("(?:€|£|\$)[^"]+")$/
     */
    public function iSetItsPriceTo($price)
    {
        $this->createSimpleProductPage->specifyPrice($price);
    }

    /**
     * @Given the product :productName should appear in the shop
     * @Given the product :productName should be in the shop
     * @Given this product should still be named :productName
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
     * @When I want to browse products
     */
    public function iWantToBrowseProducts()
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see :numberOfProducts products in the list
     */
    public function iShouldSeeProductsInTheList($numberOfProducts)
    {
        $foundRows = $this->indexPage->countItems();

        Assert::eq(
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
            $this->updateConfigurableProductPage->isCodeDisabled(),
            'Option should be immutable, but it does not.'
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

        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->createSimpleProductPage,
            $this->createConfigurableProductPage,
            $this->updateSimpleProductPage,
            $this->updateConfigurableProductPage,
        ], $product);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, $message),
            sprintf('Product %s should be required.', $element)
        );
    }
}
