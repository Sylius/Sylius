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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Element\BrowserElementInterface;
use Sylius\Behat\Element\Shop\CartWidgetElementInterface;
use Sylius\Behat\Element\Shop\CheckoutSubtotalElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Cart\SummaryPageInterface;
use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SessionManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private SummaryPageInterface $summaryPage,
        private AddressPageInterface $addressPage,
        private CheckoutSubtotalElementInterface $checkoutSubtotalElement,
        private ShowPageInterface $productShowPage,
        private CartWidgetElementInterface $cartWidgetElement,
        private NotificationCheckerInterface $notificationChecker,
        private SessionManagerInterface $sessionManager,
        private BrowserElementInterface $browserElement,
    ) {
    }

    /**
     * @Given I am on the summary of my cart page
     * @When /^I see the summary of my (?:|previous )cart$/
     * @When /^I check details of my cart$/
     */
    public function iOpenCartSummaryPage(): void
    {
        $this->summaryPage->open();
    }

    /**
     * @Given I've been gone for a long time
     */
    public function iveBeenGoneForLongTime(): void
    {
        $this->browserElement->resetSession();
    }

    /**
     * @When I update my cart
     * @When I try to update my cart
     */
    public function iUpdateMyCart(): void
    {
        $this->summaryPage->updateCart();
    }

    /**
     * @When I proceed to the checkout
     */
    public function iProceedToTheCheckout(): void
    {
        $this->summaryPage->checkout();
    }

    /**
     * @Then my cart should be empty
     * @Then my cart should be cleared
     * @Then cart should be empty with no value
     */
    public function iShouldBeNotifiedThatMyCartIsEmpty(): void
    {
        $this->summaryPage->open();

        Assert::true($this->summaryPage->isEmpty());
    }

    /**
     * @Given I removed product :productName from the cart
     * @When I remove product :productName from the cart
     */
    public function iRemoveProductFromTheCart(string $productName): void
    {
        $this->summaryPage->open();
        $this->summaryPage->removeProduct($productName);
    }

    /**
     * @When I remove :variant variant from the cart
     */
    public function iRemoveVariantFromTheCart(ProductVariantInterface $variant): void
    {
        $this->summaryPage->open();
        $product = $variant->getProduct();
        $this->summaryPage->removeProduct($product->getName());
    }

    /**
     * @Given I change :productName quantity to :quantity
     * @Given I change product :productName quantity to :quantity
     * @Given I change product :productName quantity to :quantity in my cart
     * @When the customer change product :productName quantity to :quantity in his cart
     * @When the visitor change product :productName quantity to :quantity in his cart
     */
    public function iChangeQuantityTo(string $productName, string $quantity): void
    {
        $this->summaryPage->open();
        $this->summaryPage->changeQuantity($productName, $quantity);
    }

    /**
     * @Then the grand total value should be :total
     * @Then my cart total should be :total
     * @Then the cart total should be :total
     * @Then their cart total should be :total
     */
    public function myCartTotalShouldBe(string $total): void
    {
        $this->summaryPage->open();

        Assert::same($this->summaryPage->getGrandTotal(), $total);
    }

    /**
     * @Then the grand total value in base currency should be :total
     */
    public function myBaseCartTotalShouldBe(string $total): void
    {
        $this->summaryPage->open();

        Assert::same($this->summaryPage->getBaseGrandTotal(), $total);
    }

    /**
     * @Then my cart items total should be :total
     */
    public function myCartItemsTotalShouldBe(string $itemsTotal): void
    {
        Assert::same($this->summaryPage->getItemsTotal(), $itemsTotal);
    }

    /**
     * @Then my cart taxes should be :taxTotal
     */
    public function myCartTaxesShouldBe(string $taxTotal): void
    {
        $this->summaryPage->open();

        Assert::same($this->summaryPage->getExcludedTaxTotal(), $taxTotal);
    }

    /**
     * @Then my included in price taxes should be :taxTotal
     * @Then my cart included in price taxes should be :taxTotal
     */
    public function myIncludedInPriceTaxesShouldBe(string $taxTotal): void
    {
        $this->summaryPage->open();

        Assert::same($this->summaryPage->getIncludedTaxTotal(), $taxTotal);
    }

    /**
     * @Then there should be no taxes charged
     */
    public function thereShouldBeNoTaxesCharged(): void
    {
        $this->summaryPage->open();

        Assert::false($this->summaryPage->areTaxesCharged());
    }

    /**
     * @Then my cart shipping total should be :shippingTotal
     * @Then my cart shipping should be for free
     * @Then my cart estimated shipping cost should be :shippingTotal
     */
    public function myCartShippingFeeShouldBe(string $shippingTotal = '$0.00'): void
    {
        $this->summaryPage->open();

        Assert::same($this->summaryPage->getShippingTotal(), $shippingTotal);
    }

    /**
     * @Then I should not see shipping total for my cart
     */
    public function iShouldNotSeeShippingTotalForMyCart(): void
    {
        $this->summaryPage->open();

        Assert::false($this->summaryPage->hasShippingTotal());
    }

    /**
     * @Then my discount should be :promotionsTotal
     */
    public function myDiscountShouldBe(string $promotionsTotal): void
    {
        $this->summaryPage->open();

        Assert::same($this->summaryPage->getPromotionTotal(), $promotionsTotal);
    }

    /**
     * @Given /^there should be no shipping fee$/
     */
    public function thereShouldBeNoShippingFee(): void
    {
        $this->summaryPage->open();

        try {
            $this->summaryPage->getShippingTotal();
        } catch (ElementNotFoundException) {
            return;
        }

        throw new \DomainException('Get shipping total should throw an exception!');
    }

    /**
     * @Given /^there should be no discount$/
     */
    public function thereShouldBeNoDiscount(): void
    {
        $this->summaryPage->open();

        try {
            $this->summaryPage->getPromotionTotal();
        } catch (ElementNotFoundException) {
            return;
        }

        throw new \DomainException('Get promotion total should throw an exception!');
    }

    /**
     * @Then /^(its|theirs) price should be decreased by ("[^"]+")$/
     * @Then /^(its|theirs) subtotal price should be decreased by ("[^"]+")$/
     * @Then /^(product "[^"]+") price should be decreased by ("[^"]+")$/
     */
    public function itsPriceShouldBeDecreasedBy(ProductInterface $product, int $amount): void
    {
        $this->summaryPage->open();

        $quantity = $this->summaryPage->getQuantity($product->getName());
        $itemTotal = $this->summaryPage->getItemTotal($product->getName());
        $regularUnitPrice = $this->summaryPage->getItemUnitRegularPrice($product->getName());

        Assert::same($this->getPriceFromString($itemTotal), ($quantity * $regularUnitPrice) - $amount);
    }

    /**
     * @Then /^(product "[^"]+") price should be discounted by ("[^"]+")$/
     */
    public function itsPriceShouldBeDiscountedBy(ProductInterface $product, int $amount): void
    {
        $this->summaryPage->open();

        $quantity = $this->summaryPage->getQuantity($product->getName());
        $discountedUnitPrice = $this->summaryPage->getItemUnitPrice($product->getName());
        $regularUnitPrice = $this->summaryPage->getItemUnitRegularPrice($product->getName());

        Assert::same($discountedUnitPrice, ($quantity * $regularUnitPrice) - $amount);
    }

    /**
     * @Then /^(product "[^"]+") price should not be decreased$/
     */
    public function productPriceShouldNotBeDecreased(ProductInterface $product): void
    {
        $this->summaryPage->open();

        Assert::false($this->summaryPage->isItemDiscounted($product->getName()));
    }

    /**
     * @Given /^an anonymous user added (product "([^"]+)") to the cart$/
     * @Given /^I (?:add|added) (this product) to the cart$/
     * @Given /^I have (product "[^"]+") added to the cart$/
     * @Given I added product :product to the cart
     * @Given he added product :product to the cart
     * @Given /^I (?:have|had) (product "[^"]+") in the cart$/
     * @Given /^the customer (?:added|adds) ("[^"]+" product) to the cart$/
     * @Given /^I (?:add|added) ("[^"]+" product) to the (cart)$/
     * @Given /^the visitor has (product "[^"]+") in the cart$/
     * @Given /^the customer has (product "[^"]+") in the cart$/
     * @When /^the visitor adds ("[^"]+" product) to the cart$/
     * @When I add product :product to the cart
     * @When they add product :product to the cart
     */
    public function iAddProductToTheCart(ProductInterface $product): void
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);
        $this->productShowPage->addToCart();

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @When /^I add (products "([^"]+)" and "([^"]+)") to the cart$/
     * @When /^I add (products "([^"]+)", "([^"]+)" and "([^"]+)") to the cart$/
     *
     * @param ProductInterface[] $products
     */
    public function iAddMultipleProductsToTheCart(array $products): void
    {
        foreach ($products as $product) {
            $this->iAddProductToTheCart($product);
        }
    }

    /**
     * @When /^an anonymous user in another browser adds (products "([^"]+)" and "([^"]+)") to the cart$/
     *
     * @param ProductInterface[] $products
     */
    public function anonymousUserAddsMultipleProductsToTheCart(array $products): void
    {
        $this->sessionManager->changeSession();

        foreach ($products as $product) {
            $this->iAddProductToTheCart($product);
        }
    }

    /**
     * @Given I have :variantName variant of product :product in the cart
     * @Given /^I have "([^"]+)" variant of (this product) in the cart$/
     * @When I add :variantName variant of product :product to the cart
     * @When /^I add "([^"]+)" variant of (this product) to the cart$/
     */
    public function iAddProductToTheCartSelectingVariant(string $variantName, ProductInterface $product): void
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);
        $this->productShowPage->addToCartWithVariant($variantName);

        $this->sharedStorage->set('product', $product);
        foreach ($product->getVariants() as $variant) {
            if ($variantName === ($variant->getName() ?? $variant->getDescriptor())) {
                $this->sharedStorage->set('variant', $variant);

                break;
            }
        }
    }

    /**
     * @When /^I add (\d+) of (them) to (?:the|my) cart$/
     */
    public function iAddQuantityOfProductsToTheCart(string $quantity, ProductInterface $product): void
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);
        $this->productShowPage->addToCartWithQuantity($quantity);
    }

    /**
     * @Given /^I have(?:| added) (\d+) (product(?:|s) "([^"]+)") (?:to|in) the cart$/
     * @When /^I add(?:|ed)(?:| again) (\d+) (products "([^"]+)") to the cart$/
     */
    public function iAddProductsToTheCart(string $quantity, ProductInterface $product): void
    {
        $this->productShowPage->open(['slug' => $product->getSlug()]);
        $this->productShowPage->addToCartWithQuantity($quantity);

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @When I specify product :productName quantity to :quantity
     */
    public function iSpecifyQuantityToInMyCart(string $productName, int $quantity): void
    {
        $this->summaryPage->specifyQuantity($productName, $quantity);
    }

    /**
     * @Then /^I should be(?: on| redirected to) my cart summary page$/
     * @Then I should not be able to address an order with an empty cart
     */
    public function shouldBeOnMyCartSummaryPage(): void
    {
        $this->summaryPage->waitForRedirect(3);

        $this->summaryPage->verify();
    }

    /**
     * @Then I should be notified that the product has been successfully added
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyAdded(): void
    {
        $this->notificationChecker->checkNotification('Item has been added to cart', NotificationType::success());
    }

    /**
     * @Then there should be one item in my cart
     */
    public function thereShouldBeOneItemInMyCart(): void
    {
        Assert::true($this->summaryPage->isSingleItemOnPage());
    }

    /**
     * @Then this item should have name :itemName
     */
    public function thisProductShouldHaveName(string $itemName): void
    {
        Assert::true($this->summaryPage->hasItemNamed($itemName));
    }

    /**
     * @Then this item should have variant :variantName
     */
    public function thisItemShouldHaveVariant(string $variantName): void
    {
        Assert::true($this->summaryPage->hasItemWithVariantNamed($variantName));
    }

    /**
     * @Then this item should have code :variantCode
     */
    public function thisItemShouldHaveCode(string $variantCode): void
    {
        Assert::true($this->summaryPage->hasItemWithCode($variantCode));
    }

    /**
     * @When I view my cart in the previous session
     */
    public function iViewMyCartInPreviousSession(): void
    {
        $this->sessionManager->restorePreviousSession();

        $this->summaryPage->open();
    }

    /**
     * @Given I have :product with :productOption :productOptionValue in the cart
     * @Given I have product :product with product option :productOption :productOptionValue in the cart
     * @When I add :product with :productOption :productOptionValue to the cart
     */
    public function iAddThisProductWithToTheCart(
        ProductInterface $product,
        ProductOptionInterface $productOption,
        string $productOptionValue,
    ): void {
        $this->productShowPage->open(['slug' => $product->getSlug()]);

        $this->productShowPage->addToCartWithOption($productOption, $productOptionValue);
    }

    /**
     * @Given /^(this product) should have ([^"]+) "([^"]+)"$/
     */
    public function thisItemShouldHaveOptionValue(ProductInterface $product, string $optionName, string $optionValue): void
    {
        Assert::true($this->summaryPage->hasItemWithOptionValue($product->getName(), $optionName, $optionValue));
    }

    /**
     * @When I clear my cart
     */
    public function iClearMyCart(): void
    {
        $this->summaryPage->clearCart();
    }

    /**
     * @Then /^I should see "([^"]+)" with quantity (\d+) in my cart$/
     * @Then /^the visitor should see product "([^"]+)" with quantity (\d+) in his cart$/
     * @Then /^the customer should see product "([^"]+)" with quantity (\d+) in his cart$/
     */
    public function iShouldSeeWithQuantityInMyCart(string $productName, int $quantity): void
    {
        Assert::same($this->summaryPage->getQuantity($productName), $quantity);
    }

    /**
     * @Then /^the customer can see "([^"]+)" product in the cart$/
     * @Then /^the visitor can see "([^"]+)" product in the cart$/
     */
    public function theCustomerCanSeeProductInTheCart(string $productName): void
    {
        Assert::true(
            $this->summaryPage->hasItemNamed($productName),
            sprintf('Product with name "%s" was not found in the cart.', $productName),
        );
    }

    /**
     * @Then /^I should see(?:| also) "([^"]+)" with unit price ("[^"]+") in my cart$/
     * @Then /^I should see(?:| also) "([^"]+)" with discounted unit price ("[^"]+") in my cart$/
     * @Then /^the product "([^"]+)" should have discounted unit price ("[^"]+") in the cart$/
     */
    public function iShouldSeeProductWithUnitPriceInMyCart(string $productName, int $unitPrice): void
    {
        Assert::same($this->summaryPage->getItemUnitPrice($productName), $unitPrice);
    }

    /**
     * @Then /^the product "([^"]+)" should have total price ("[^"]+") in the cart$/
     */
    public function theProductShouldHaveTotalPrice(string $productName, string $totalPrice): void
    {
        Assert::same($this->summaryPage->getItemTotal($productName), $totalPrice);
    }

    /**
     * @Then /^I should see "([^"]+)" with original price ("[^"]+") in my cart$/
     */
    public function iShouldSeeWithOriginalPriceInMyCart(string $productName, int $originalPrice): void
    {
        Assert::same($this->summaryPage->getItemUnitRegularPrice($productName), $originalPrice);
    }

    /**
     * @Then /^I should see "([^"]+)" only with unit price ("[^"]+") in my cart$/
     */
    public function iShouldSeeOnlyWithUnitPriceInMyCart(string $productName, int $unitPrice): void
    {
        $this->iShouldSeeProductWithUnitPriceInMyCart($productName, $unitPrice);
        Assert::false($this->summaryPage->hasOriginalPrice($productName));
    }

    /**
     * @Given I use coupon with code :couponCode
     */
    public function iUseCouponWithCode(string $couponCode): void
    {
        $this->summaryPage->applyCoupon($couponCode);
    }

    /**
     * @Then I should be notified that the coupon is invalid
     */
    public function iShouldBeNotifiedThatCouponIsInvalid(): void
    {
        Assert::same($this->summaryPage->getPromotionCouponValidationMessage(), 'Coupon code is invalid.');
    }

    /**
     * @Then total price of :productName item should be :productPrice
     */
    public function thisItemPriceShouldBe(string $productName, string $productPrice): void
    {
        $this->summaryPage->open();

        Assert::same($this->summaryPage->getItemTotal($productName), $productPrice);
    }

    /**
     * @Then /^I should be notified that (this product) has insufficient stock$/
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product): void
    {
        Assert::true($this->summaryPage->hasItemWithInsufficientStock($product->getName()));
    }

    /**
     * @Then /^I should not be notified that (this product) cannot be updated$/
     */
    public function iShouldNotBeNotifiedThatThisProductCannotBeUpdated(ProductInterface $product): void
    {
        Assert::false($this->summaryPage->hasProductOutOfStockValidationMessage($product));
    }

    /**
     * @Then my cart's total should be :total
     */
    public function myCartSTotalShouldBe(string $total): void
    {
        $this->summaryPage->open();

        Assert::same($this->summaryPage->getCartTotal(), $total);
    }

    /**
     * @Then /^(\d)(?:st|nd|rd|th) item in my cart should have "([^"]+)" image displayed$/
     */
    public function itemShouldHaveImageDisplayed(int $itemNumber, string $image): void
    {
        Assert::contains($this->summaryPage->getItemImage($itemNumber), $image);
    }

    /**
     * @Then I should see cart total quantity is :totalQuantity
     */
    public function iShouldSeeCartTotalQuantity(int $totalQuantity): void
    {
        Assert::same($this->cartWidgetElement->getCartTotalQuantity(), $totalQuantity);
    }

    /**
     * @Then I should be on the checkout addressing page
     */
    public function iShouldBeOnTheCheckoutAddressingStep(): void
    {
        $this->addressPage->verify();
    }

    /**
     * @Then the quantity of :productName should be :quantity
     */
    public function theQuantityOfShouldBe(string $productName, int $quantity): void
    {
        Assert::same($this->checkoutSubtotalElement->getProductQuantity($productName), $quantity);
    }

    /**
     * @Then I should see an empty cart
     */
    public function iShouldSeeAnEmptyCart(): void
    {
        Assert::true($this->summaryPage->isEmpty());
    }

    private function getPriceFromString(string $price): int
    {
        return (int) round((float) str_replace(['€', '£', '$'], '', $price) * 100, 2);
    }
}
