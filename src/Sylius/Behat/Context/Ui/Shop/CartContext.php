<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Cart\SummaryPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CartContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var SummaryPageInterface
     */
    private $summaryPage;

    /**
     * @var ShowPageInterface
     */
    private $productShowPage;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param SummaryPageInterface $summaryPage
     * @param ShowPageInterface $productShowPage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        SummaryPageInterface $summaryPage,
        ShowPageInterface $productShowPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->summaryPage = $summaryPage;
        $this->productShowPage = $productShowPage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I see the summary of my cart
     */
    public function iOpenCartSummaryPage()
    {
        $this->summaryPage->open();
    }

    /**
     * @Then my cart should be empty
     * @Then cart should be empty with no value
     */
    public function iShouldBeNotifiedThatMyCartIsEmpty()
    {
        $this->summaryPage->open();

        Assert::true(
             $this->summaryPage->isEmpty(),
            'There should appear information about empty cart, but it does not.'
        );
    }

    /**
     * @Given /^I (?:remove|removed) product "([^"]+)" from the cart$/
     */
    public function iRemoveProductFromTheCart($productName)
    {
        $this->summaryPage->open();
        $this->summaryPage->removeProduct($productName);
    }

    /**
     * @Given I change :productName quantity to :quantity
     */
    public function iChangeQuantityTo($productName, $quantity)
    {
        $this->summaryPage->open();
        $this->summaryPage->changeQuantity($productName, $quantity);
    }

    /**
     * @Then grand total value should be :total
     * @Then my cart total should be :total
     */
    public function myCartTotalShouldBe($total)
    {
        $this->summaryPage->open();
        Assert::same(
            $this->summaryPage->getGrandTotal(),
            $total,
            'Grand total should be %2$s, but it is %s.'
        );
    }

    /**
     * @Then tax total value should be :taxTotal
     * @Then my cart taxes should be :taxTotal
     */
    public function myCartTaxesShouldBe($taxTotal)
    {
        $this->summaryPage->open();

        Assert::same(
            $this->summaryPage->getTaxTotal(),
            $taxTotal,
            'Tax total value should be %2$s, but it is %s.'
        );
    }

    /**
     * @Then shipping total value should be :shippingTotal
     * @Then my cart shipping total should be :shippingTotal
     * @Then my cart shipping should be for free
     */
    public function myCartShippingFeeShouldBe($shippingTotal = '€0.00')
    {
        $this->summaryPage->open();

        Assert::same(
            $this->summaryPage->getShippingTotal(),
            $shippingTotal,
            'Shipping total value should be %2$s, but it is %s.'
        );
    }

    /**
     * @Then my cart promotions should be :promotionsTotal
     * @Then my discount should be :promotionsTotal
     */
    public function myDiscountShouldBe($promotionsTotal)
    {
        $this->summaryPage->open();

        Assert::same(
            $this->summaryPage->getPromotionTotal(),
            $promotionsTotal,
            'Promotion total value should be %2$s, but it is %s.'
        );
    }

    /**
     * @Given /^there should be no shipping fee$/
     */
    public function thereShouldBeNoShippingFee()
    {
        $this->summaryPage->open();

        expect($this->summaryPage)->toThrow(ElementNotFoundException::class)->during('getShippingTotal', []);
    }

    /**
     * @Given /^there should be no discount$/
     */
    public function thereShouldBeNoDiscount()
    {
        $this->summaryPage->open();

        expect($this->summaryPage)->toThrow(ElementNotFoundException::class)->during('getPromotionTotal', []);
    }

    /**
     * @Then /^(its|theirs) price should be decreased by ("[^"]+")$/
     * @Then /^(product "[^"]+") price should be decreased by ("[^"]+")$/
     */
    public function itsPriceShouldBeDecreasedBy(ProductInterface $product, $amount)
    {
        $this->summaryPage->open();

        $discountPrice = $this->summaryPage->getItemDiscountPrice($product->getName());
        $regularPrice = $this->summaryPage->getItemRegularPrice($product->getName());

        Assert::same(
            $discountPrice,
            ($regularPrice - $amount),
            'Price after discount should be %2$s, but it is %s.'
        );
    }

    /**
     * @Given /^(product "[^"]+") price should not be decreased$/
     */
    public function productPriceShouldNotBeDecreased(ProductInterface $product)
    {
        $this->summaryPage->open();

        Assert::false(
            $this->summaryPage->isItemDiscounted($product->getName()),
            'The price should not be decreased, but it is.'
        );
    }


    /**
     * @Given /^I add (this product) to the cart$/
     * @Given I added product :product to the cart
     * @Given /^I (?:have|had) (product "([^"]+)") in the cart$/
     * @When I add product :product to the cart
     */
    public function iAddProductToTheCart(ProductInterface $product)
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);
        $this->productShowPage->addToCart();

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @Given /^I added (products "([^"]+)" and "([^"]+)") to the cart$/
     * @When /^I add (products "([^"]+)" and "([^"]+)") to the cart$/
     * @Given /^I added (products "([^"]+)", "([^"]+)" and "([^"]+)") to the cart$/
     * @When /^I add (products "([^"]+)", "([^"]+)" and "([^"]+)") to the cart$/
     */
    public function iAddMultipleProductsToTheCart(array $products)
    {
        foreach ($products as $product) {
            $this->iAddProductToTheCart($product);
        }
    }

    /**
     * @Given I added :variant variant of product :product to the cart
     * @When I add :variant variant of product :product to the cart
     * @When I have :variant variant of product :product in the cart
     * @When /^I add "([^"]+)" variant of (this product) to the cart$/
     */
    public function iAddProductToTheCartSelectingVariant($variant, ProductInterface $product)
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);
        $this->productShowPage->addToCartWithVariant($variant);

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @Given I have :quantity products :product in the cart
     * @When I add :quantity products :product to the cart
     */
    public function iAddProductsToTheCart(ProductInterface $product, $quantity)
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);
        $this->productShowPage->addToCartWithQuantity($quantity);

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @Then I should be on my cart summary page
     */
    public function shouldBeOnMyCartSummaryPage()
    {
        Assert::true(
            $this->summaryPage->isOpen(),
            'Cart summary page should be open, but it does not.'
        );
    }

    /**
     * @Then I should be notified that the product has been successfully added
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyAdded()
    {
        $this->notificationChecker->checkNotification('Item has been added to cart.', NotificationType::success());
    }

    /**
     * @Then there should be one item in my cart
     */
    public function thereShouldBeOneItemInMyCart()
    {
        Assert::true(
            $this->summaryPage->isSingleItemOnPage(),
            'There should be only one item on list, but it does not.'
        );
    }

    /**
     * @Then this item should have name :itemName
     */
    public function thisProductShouldHaveName($itemName)
    {
        Assert::true(
            $this->summaryPage->isItemWithName($itemName),
            sprintf('The product with name %s should appear on the list, but it does not.', $itemName)
        );
    }

    /**
     * @Then this item should have variant :variantName
     */
    public function thisItemShouldHaveVariant($variantName)
    {
        Assert::true(
            $this->summaryPage->isItemWithVariant($variantName),
            sprintf('The product with variant %s should appear on the list, but it does not.', $variantName)
        );
    }

    /**
     * @When I add :product with :productOption :productOptionValue to the cart
     */
    public function iAddThisProductWithToTheCart(ProductInterface $product, OptionInterface $productOption, $productOptionValue)
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);

        $this->productShowPage->addToCartWithOption($productOption, $productOptionValue);
    }

    /**
     * @Given /^(this product) should have ([^"]+) "([^"]+)"$/
     */
    public function thisItemShouldHaveSize(ProductInterface $product, $optionName, $optionValue)
    {
        Assert::contains(
            $this->summaryPage->getProductOption($product->getName(), $optionName),
            $optionValue,
            'The product should have option with value %2$s, but it has option with value %s.'
        );
    }

    /**
     * @When I clear my cart
     */
    public function iClearMyCart()
    {
        $this->summaryPage->clearCart();
    }

    /**
     * @Then /^I should see "([^"]+)" with quantity (\d+) in my cart$/
     */
    public function iShouldSeeWithQuantityInMyCart($productName, $quantity)
    {
        Assert::same(
            $this->summaryPage->getQuantity($productName),
            $quantity,
            'The quantity of product should be %2$s, but it is %s'
        );
    }
}
