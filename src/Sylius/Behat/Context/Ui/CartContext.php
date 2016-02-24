<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Cart\CartSummaryPage;
use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\Product\ProductShowPage;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CartContext implements Context
{
    /**
     * @var CartSummaryPage
     */
    private $cartSummaryPage;

    /**
     * @var ProductShowPage
     */
    private $productShowPage;

    /**
     * @param CartSummaryPage $cartSummaryPage
     * @param ProductShowPage $productShowPage
     */
    public function __construct(
        CartSummaryPage $cartSummaryPage,
        ProductShowPage $productShowPage
    ) {
        $this->cartSummaryPage = $cartSummaryPage;
        $this->productShowPage = $productShowPage;
    }

    /**
     * @Given I added product :product to the cart
     * @Given /^I (?:have|had) (product "([^"]+)") in the cart$/
     * @When I add product :product to the cart
     */
    public function iAddProductToTheCart(ProductInterface $product)
    {
        $this->productShowPage->open(['product' => $product]);
        $this->productShowPage->addToCart();
    }

    /**
     * @Given I added :variant variant of product :product to the cart
     * @When I add :variant variant of product :product to the cart
     * @When I have :variant variant of product :product in the cart
     */
    public function iAddProductToTheCartSelectingVariant($variant, ProductInterface $product)
    {
        $this->productShowPage->open(['product' => $product]);
        $this->productShowPage->addToCartWithVariant($variant);
    }

    /**
     * @Given /^I (?:remove|removed) product "([^"]+)" from the cart$/
     */
    public function iRemoveProductFromTheCart($productName)
    {
        $this->cartSummaryPage->open();
        $this->cartSummaryPage->removeProduct($productName);
    }

    /**
     * @Given I change :productName quantity to :quantity
     */
    public function iChangeQuantityTo($productName, $quantity)
    {
        $this->cartSummaryPage->open();
        $this->cartSummaryPage->changeQuantity($productName, $quantity);
    }

    /**
     * @Given I have :quantity products :product in the cart
     * @When I add :quantity products :product to the cart
     */
    public function iAddProductsToTheCart(ProductInterface $product, $quantity)
    {
        $this->productShowPage->open(['product' => $product]);
        $this->productShowPage->addToCartWithQuantity($quantity);
    }

    /**
     * @Then my cart total should be :total
     */
    public function myCartTotalShouldBe($total)
    {
        $this->cartSummaryPage->open();

        expect($this->cartSummaryPage->getGrandTotal())->toBe($total);
    }

    /**
     * @Then my cart taxes should be :taxTotal
     */
    public function myCartTaxesShouldBe($taxTotal)
    {
        $this->cartSummaryPage->open();

        expect($this->cartSummaryPage->getTaxTotal())->toBe($taxTotal);
    }

    /**
     * @Then my cart shipping fee should be :shippingTotal
     */
    public function myCartShippingFeeShouldBe($shippingTotal)
    {
        $this->cartSummaryPage->open();

        expect($this->cartSummaryPage->getShippingTotal())->toBe($shippingTotal);
    }

    /**
     * @Then my cart promotions should be :promotionsTotal
     * @Then my discount should be :promotionsTotal
     */
    public function myDiscountShouldBe($promotionsTotal)
    {
        $this->cartSummaryPage->open();

        expect($this->cartSummaryPage->getPromotionTotal())->toBe($promotionsTotal);
    }

    /**
     * @Given /^cart should be empty with no value$/
     */
    public function cartShouldBeEmptyWithNoValue()
    {
        $this->cartSummaryPage->open();

        expect($this->cartSummaryPage)->toThrow(ElementNotFoundException::class)->during('getGrandTotal', []);
    }

    /**
     * @Given /^there should be no shipping fee$/
     */
    public function thereShouldBeNoShippingFee()
    {
        $this->cartSummaryPage->open();

        expect($this->cartSummaryPage)->toThrow(ElementNotFoundException::class)->during('getShippingTotal', []);
    }

    /**
     * @Given /^there should be no discount$/
     */
    public function thereShouldBeNoDiscount()
    {
        $this->cartSummaryPage->open();

        expect($this->cartSummaryPage)->toThrow(ElementNotFoundException::class)->during('getPromotionTotal', []);
    }
}
