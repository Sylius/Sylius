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
use Sylius\Behat\Page\Product\ProductShowPage;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CartContext implements Context
{
    /**
     * @var ProductShowPage
     */
    private $productShowPage;

    /**
     * @var CartSummaryPage
     */
    private $cartSummaryPage;

    /**
     * @param ProductShowPage $productShowPage
     * @param CartSummaryPage $cartSummaryPage
     */
    public function __construct(
        ProductShowPage $productShowPage,
        CartSummaryPage $cartSummaryPage
    ) {
        $this->productShowPage = $productShowPage;
        $this->cartSummaryPage = $cartSummaryPage;
    }

    /**
     * @Given I added product :product to the cart
     * @When I add product :product to the cart
     * @When I have product :product in the cart
     */
    public function iAddProductToTheCart(ProductInterface $product)
    {
        $this->productShowPage->open(['product' => $product]);
        $this->productShowPage->addToCart();
    }

    /**
     * @Given I remove product :productName from the cart
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
     * @Given /^my cart shipping fee should be "([^"]+)"$/
     */
    public function myCartShippingFeeShouldBe($shippingTotal)
    {
        $this->assertCartSummaryPageContents('Shipping', $shippingTotal);
    }

    /**
     * @param string $type
     * @param string $value
     */
    public function assertCartSummaryPageContents($type, $value)
    {
        /** @var CartSummaryPage $cartSummaryPage */
        $cartSummaryPage = $this->getPage('Cart\CartSummaryPage');
        $cartSummaryPage->open();

        $this->assertSession()->elementTextContains('css', '#cart-summary', $type . ' total: ' . $value);
    }
}
