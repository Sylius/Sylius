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
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Behat\Page\Shop\Taxon\ShowPageInterface as TaxonShowPageInterface;
use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @var ShowPageInterface
     */
    private $showPage;

    /**
     * @var TaxonShowPageInterface
     */
    private $taxonShowPage;

    /**
     * @param ShowPageInterface $showPage
     * @param TaxonShowPageInterface $taxonShowPage
     */
    public function __construct(
        ShowPageInterface $showPage,
        TaxonShowPageInterface $taxonShowPage
    ) {
        $this->showPage = $showPage;
        $this->taxonShowPage = $taxonShowPage;
    }

    /**
     * @Then I should be able to access product :product
     */
    public function iShouldBeAbleToAccessProduct(ProductInterface $product)
    {
        $this->showPage->tryToOpen(['slug' => $product->getSlug()]);

        Assert::true(
            $this->showPage->isOpen(['slug' => $product->getSlug()]),
            'Product show page should be open, but it does not.'
        );
    }

    /**
     * @Then I should not be able to access product :product
     */
    public function iShouldNotBeAbleToAccessProduct(ProductInterface $product)
    {
        $this->showPage->tryToOpen(['slug' => $product->getSlug()]);

        Assert::false(
            $this->showPage->isOpen(['slug' => $product->getSlug()]),
            'Product show page should not be open, but it does.'
        );
    }

    /**
     * @When /^I check (this product)'s details/
     * @When I view product :product
     */
    public function iOpenProductPage(ProductInterface $product)
    {
        $this->showPage->open(['slug' => $product->getSlug()]);
    }

    /**
     * @Given I should see the product name :name
     */
    public function iShouldSeeProductName($name)
    {
        Assert::same(
            $name,
            $this->showPage->getName(),
            'Product should have name %2$s, but it has %s'
        );
    }

    /**
     * @When I open page :url
     */
    public function iOpenPage($url)
    {
        $this->showPage->visit($url);
    }

    /**
     * @Then I should be on :product product detailed page
     * @Then I should still be on product :product page
     */
    public function iShouldBeOnProductDetailedPage(ProductInterface $product)
    {
        Assert::true(
            $this->showPage->isOpen(['slug' => $product->getSlug()]),
            sprintf('Product %s show page should be open, but it does not.', $product->getName())
        );
    }

    /**
     * @Then I should see the product attribute :attributeName with value :AttributeValue
     */
    public function iShouldSeeTheProductAttributeWithValue($attributeName, $AttributeValue)
    {
        Assert::true(
            $this->showPage->hasAttributeWithValue($attributeName, $AttributeValue),
            sprintf('Product should have attribute %s with value %s, but it does not.', $attributeName, $AttributeValue)
        );
    }
    
    /**
     * @When /^I browse products from (taxon "([^"]+)")$/
     */
    public function iCheckListOfProductsForTaxon(TaxonInterface $taxon)
    {
        $this->taxonShowPage->open(['permalink' => $taxon->getPermalink()]);
    }

    /**
     * @Then I should see the product :productName
     */
    public function iShouldSeeProduct($productName)
    {
        Assert::true(
            $this->taxonShowPage->isProductOnList($productName),
            sprintf("The product %s should appear on page, but it does not.", $productName)
        );
    }

    /**
     * @Then I should not see the product :productName
     */
    public function iShouldNotSeeProduct($productName)
    {
        Assert::false(
            $this->taxonShowPage->isProductOnList($productName),
            sprintf("The product %s should not appear on page, but it does.", $productName)
        );
    }

    /**
     * @Then I should see empty list of products
     */
    public function iShouldSeeEmptyListOfProducts()
    {
        Assert::true(
            $this->taxonShowPage->isEmpty(),
            'There should appear information about empty list of products, but it does not.'
        );
    }

    /**
     * @Then I should see that it is out of stock
     */
    public function iShouldSeeItIsOutOfStock()
    {
        Assert::true(
            $this->showPage->isOutOfStock(),
            'Out of stock label should be visible.'
        );
    }

    /**
     * @Then I should be unable to add it to the cart
     */
    public function iShouldBeUnableToAddItToTheCart()
    {
        Assert::false(
            $this->showPage->hasAddToCartButton(),
            'Add to cart button should not be visible.'
        );
    }

    /**
     * @Then the product price should be :price
     * @Then I should see the product price :price
     */
    public function iShouldSeeTheProductPrice($price)
    {
        Assert::same(
            $price,
            $this->showPage->getPrice(),
            'Product should have price %2$s, but it has %s'
        );
    }

    /**
     * @When I set its :optionName to :optionValue
     */
    public function iSetItsOptionTo($optionName, $optionValue)
    {
        $this->showPage->selectOption($optionName, $optionValue);
    }

    /**
     * @When I select :variantName variant
     */
    public function iSelectVariant($variantName)
    {
        $this->showPage->selectVariant($variantName);
    }

    /**
     * @Then I should see the product :productName with price :productPrice
     */
    public function iShouldSeeTheProductWithPrice($productName, $productPrice)
    {
        Assert::true(
            $this->taxonShowPage->isProductWithPriceOnList($productName, $productPrice),
            sprintf("The product %s with price %s should appear on page, but it does not.", $productName, $productPrice)
        );
    }

    /**
     * @Then /^I should be notified that (this product) does not have sufficient stock$/
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product)
    {
       $this->showPage->waitForValidationErrors(3);

        Assert::true(
            $this->showPage->hasProductOutOfStockValidationMessage($product),
            sprintf('I should see validation message for %s product', $product->getName())
        );
    }

    /**
     * @Then /^I should not be notified that (this product) does not have sufficient stock$/
     */
    public function iShouldNotBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product)
    {
        Assert::false(
            $this->showPage->hasProductOutOfStockValidationMessage($product),
            sprintf('I should see validation message for %s product', $product->getName())
        );
    }

    /**
     * @Then I should see a main image
     */
    public function iShouldSeeAMainImage()
    {
        Assert::true(
            $this->showPage->isMainImageDisplayed(),
            'The main image should have been displayed.'
        );
    }
}
