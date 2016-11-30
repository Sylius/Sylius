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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
     * @Given I am at the summary of my cart
     * @When I see the summary of my cart
     */
    public function iOpenCartSummaryPage()
    {
        $this->summaryPage->open();
    }

    /**
     * @When I update my cart
     */
    public function iUpdateMyCart()
    {
        $this->summaryPage->updateCart();
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
     * @Then the grand total value should be :total
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
     * @Then the grand total value in base currency should be :total
     */
    public function myBaseCartTotalShouldBe($total)
    {
        $this->summaryPage->open();

        Assert::same(
            $this->summaryPage->getBaseGrandTotal(),
            $total,
            'Base grand total should be %2$s, but it is %s.'
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
    public function myCartShippingFeeShouldBe($shippingTotal = '$0.00')
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

        try {
            $this->summaryPage->getShippingTotal();
        } catch (ElementNotFoundException $exception) {
            return;
        }

        throw new \DomainException('Get shipping total should throw an exception!');
    }

    /**
     * @Given /^there should be no discount$/
     */
    public function thereShouldBeNoDiscount()
    {
        $this->summaryPage->open();

        try {
            $this->summaryPage->getPromotionTotal();
        } catch (ElementNotFoundException $exception) {
            return;
        }

        throw new \DomainException('Get promotion total should throw an exception!');
    }

    /**
     * @Then /^(its|theirs) price should be decreased by ("[^"]+")$/
     * @Then /^(product "[^"]+") price should be decreased by ("[^"]+")$/
     */
    public function itsPriceShouldBeDecreasedBy(ProductInterface $product, $amount)
    {
        $this->summaryPage->open();

        $quantity = $this->summaryPage->getQuantity($product->getName());
        $itemTotal = $this->summaryPage->getItemTotal($product->getName());
        $regularUnitPrice = $this->summaryPage->getItemUnitRegularPrice($product->getName());

        Assert::same(
            ($quantity * $regularUnitPrice) - $amount,
            $this->getPriceFromString($itemTotal),
            'Price after discount should be %s, but it is %2$s.'
        );
    }

    /**
     * @Then /^(product "[^"]+") price should not be decreased$/
     * @Then /^(its|theirs) price should not be decreased$/
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
     * @Given /^I (?:add|added) (this product) to the cart$/
     * @Given I added product :product to the cart
     * @Given /^I (?:have|had) (product "[^"]+") in the cart$/
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
     * @Given I added :variantName variant of product :product to the cart
     * @When I add :variantName variant of product :product to the cart
     * @When I have :variantName variant of product :product in the cart
     * @When /^I add "([^"]+)" variant of (this product) to the cart$/
     */
    public function iAddProductToTheCartSelectingVariant($variantName, ProductInterface $product)
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);
        $this->productShowPage->addToCartWithVariant($variantName);

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @When /^I add (\d+) of (them) to (?:the|my) cart$/
     */
    public function iAddQuantityOfProductsToTheCart($quantity, ProductInterface $product)
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);
        $this->productShowPage->addToCartWithQuantity($quantity);
    }

    /**
     * @Given /^I have(?:| added) (\d+) (products "([^"]+)") (?:to|in) the cart$/
     * @When /^I add(?:|ed)(?:| again) (\d+) (products "([^"]+)") to the cart$/
     */
    public function iAddProductsToTheCart($quantity, ProductInterface $product)
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);
        $this->productShowPage->addToCartWithQuantity($quantity);

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @Then /^I should be(?: on| redirected to) my cart summary page$/
     */
    public function shouldBeOnMyCartSummaryPage()
    {
        $this->summaryPage->waitForRedirect(3);

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
        $this->notificationChecker->checkNotification('Item has been added to cart', NotificationType::success());
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
            $this->summaryPage->hasItemNamed($itemName),
            sprintf('The product with name %s should appear on the list, but it does not.', $itemName)
        );
    }

    /**
     * @Then this item should have variant :variantName
     */
    public function thisItemShouldHaveVariant($variantName)
    {
        Assert::true(
            $this->summaryPage->hasItemWithVariantNamed($variantName),
            sprintf('The product with variant %s should appear on the list, but it does not.', $variantName)
        );
    }

    /**
     * @Then this item should have code :variantCode
     */
    public function thisItemShouldHaveCode($variantCode)
    {
        Assert::true(
            $this->summaryPage->hasItemWithCode($variantCode),
            sprintf('The product with code %s should appear on the list, but it does not.', $variantCode)
        );
    }

    /**
     * @Given I have :product with :productOption :productOptionValue in the cart
     * @When I add :product with :productOption :productOptionValue to the cart
     */
    public function iAddThisProductWithToTheCart(ProductInterface $product, ProductOptionInterface $productOption, $productOptionValue)
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);

        $this->productShowPage->addToCartWithOption($productOption, $productOptionValue);
    }

    /**
     * @Given /^(this product) should have ([^"]+) "([^"]+)"$/
     */
    public function thisItemShouldHaveOptionValue(ProductInterface $product, $optionName, $optionValue)
    {
        Assert::true(
            $this->summaryPage->hasItemWithOptionValue($product->getName(), $optionName, $optionValue),
            sprintf('Product in cart "%s" should have option %s with value %s, but it has not.', $product->getName(), $optionName, $optionValue)
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
            (int) $quantity,
            'The quantity of product should be %2$s, but it is %s'
        );
    }

    /**
     * @Then /^I should see "([^"]+)" with unit price ("[^"]+") in my cart$/
     */
    public function iShouldSeeProductWithUnitPriceInMyCart($productName, $unitPrice)
    {
        Assert::same(
            $this->summaryPage->getItemUnitPrice($productName),
            $unitPrice,
            'The unit price of product should be %2$s, but it is %s.'
        );
    }

    /**
     * @Given I use coupon with code :couponCode
     */
    public function iUseCouponWithCode($couponCode)
    {
        $this->summaryPage->applyCoupon($couponCode);
    }

    /**
     * @Then I should be notified that the coupon is invalid
     */
    public function iShouldBeNotifiedThatCouponIsInvalid()
    {
        Assert::same(
            $this->summaryPage->getPromotionCouponValidationMessage(),
            'Coupon code is invalid.'
        );
    }

    /**
     * @Then total price of :productName item should be :productPrice
     */
    public function thisItemPriceShouldBe($productName, $productPrice)
    {
        $this->summaryPage->open();

        Assert::same(
            $this->summaryPage->getItemTotal($productName),
            $productPrice
        );
    }

    /**
     * @Then /^I should be notified that (this product) cannot be updated$/
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product)
    {
        Assert::true(
            $this->summaryPage->hasProductOutOfStockValidationMessage($product),
            sprintf('I should see validation message for %s product', $product->getName())
        );
    }

    /**
     * @Then /^I should not be notified that (this product) cannot be updated$/
     */
    public function iShouldNotBeNotifiedThatThisProductCannotBeUpdated(ProductInterface $product)
    {
        Assert::false(
            $this->summaryPage->hasProductOutOfStockValidationMessage($product),
            sprintf('I should see validation message for %s product', $product->getName())
        );
    }

    /**
     * @Then my cart's total should be :total
     */
    public function myCartSTotalShouldBe($total)
    {
        Assert::same(
            $total,
            $this->summaryPage->getCartTotal(),
            'Cart should have %s total, but it has %2$s.'
        );
    }

    /**
     * @param string $price
     *
     * @return int
     */
    private function getPriceFromString($price)
    {
        return (int) round((str_replace(['€', '£', '$'], '', $price) * 100), 2);
    }
}
