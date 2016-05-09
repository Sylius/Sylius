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
use Sylius\Behat\Page\Shop\Cart\CartSummaryPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CartContext implements Context
{
    /**
     * @var CartSummaryPageInterface
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
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param CartSummaryPageInterface $summaryPage
     * @param ShowPageInterface $productShowPage
     * @param NotificationCheckerInterface $notificationChecker
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        CartSummaryPageInterface $summaryPage,
        ShowPageInterface $productShowPage,
        NotificationCheckerInterface $notificationChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->summaryPage = $summaryPage;
        $this->productShowPage = $productShowPage;
        $this->notificationChecker = $notificationChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I open cart summary page
     */
    public function iOpenCartSummaryPage()
    {
        $this->summaryPage->open();
    }

    /**
     * @Then I should be notified that my cart is empty
     */
    public function iShouldBeNotifiedThatMyCartIsEmpty()
    {
        $this->notificationChecker->checkNotification('Your cart is empty', NotificationType::information());
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
        Assert::eq(
            $this->summaryPage->getGrandTotal(),
            $total,
            'Grand total should be %2$s, but it is %s'
        );
    }

    /**
     * @Then tax total value should be :taxTotal
     * @Then my cart taxes should be :taxTotal
     */
    public function myCartTaxesShouldBe($taxTotal)
    {
        $this->summaryPage->open();

        expect($this->summaryPage->getTaxTotal())->toBe($taxTotal);
    }

    /**
     * @Then shipping total value should be :shippingTotal
     * @Then my cart shipping fee should be :shippingTotal
     */
    public function myCartShippingFeeShouldBe($shippingTotal)
    {
        $this->summaryPage->open();

        expect($this->summaryPage->getShippingTotal())->toBe($shippingTotal);
    }

    /**
     * @Then my cart promotions should be :promotionsTotal
     * @Then my discount should be :promotionsTotal
     */
    public function myDiscountShouldBe($promotionsTotal)
    {
        $this->summaryPage->open();

        expect($this->summaryPage->getPromotionTotal())->toBe($promotionsTotal);
    }

    /**
     * @Given /^cart should be empty with no value$/
     */
    public function cartShouldBeEmptyWithNoValue()
    {
        $this->summaryPage->open();

        expect($this->summaryPage)->toThrow(ElementNotFoundException::class)->during('getGrandTotal', []);
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

        $discountPrice = $this->getPriceFromString($this->summaryPage->getItemDiscountPrice($product->getName()));
        $regularPrice = $this->getPriceFromString($this->summaryPage->getItemRegularPrice($product->getName()));

        expect($discountPrice)->toBe($regularPrice - $amount);
    }

    /**
     * @Given /^(product "[^"]+") price should not be decreased$/
     */
    public function productPriceShouldNotBeDecreased(ProductInterface $product)
    {
        $this->summaryPage->open();

        expect($this->summaryPage->isItemDiscounted($product->getName()))->toBe(false);
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
     * @Then I should see :elementName
     */
    public function iShouldSeeElement($elementName)
    {
        Assert::true(
            $this->summaryPage->isElementOnPage($elementName),
            sprintf('Element %s should appear on the page, but it does not.', $elementName)
         );
    }

    /**
     * @Given unit price value should be :price
     */
    public function unitPriceValueShouldBe($price)
    {
        $this->summaryPage->open();
        Assert::eq(
            $this->summaryPage->getUnitPrice(),
            $price,
            'Unit price should be %2$s, but it is %s'
        );
    }

    /**
     * @Given total value should be :total
     */
    public function totalValueShouldBe($total)
    {
        $this->summaryPage->open();
        Assert::eq(
            $this->summaryPage->getTotal(),
            $total,
            'Total should be %2$s, but it is %s'
        );
    }

    /**
     * @Given quantity value should be :quantity
     */
    public function quantityValueShouldBe($quantity)
    {
        $this->summaryPage->open();
        Assert::eq(
            $this->summaryPage->getQuantity(),
            $quantity,
            'Quantity of product should be %2$s, but it is %s'
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
