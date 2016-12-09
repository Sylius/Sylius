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
use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Shop\ProductReview\IndexPageInterface as ProductReviewIndexPageInterface;
use Sylius\Behat\Page\Shop\Product\IndexPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
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
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var ProductReviewIndexPageInterface
     */
    private $productReviewsIndexPage;

    /**
     * @param ShowPageInterface $showPage
     * @param IndexPageInterface $indexPage
     * @param ProductReviewIndexPageInterface $productReviewsIndexPage
     */
    public function __construct(
        ShowPageInterface $showPage,
        IndexPageInterface $indexPage,
        ProductReviewIndexPageInterface $productReviewsIndexPage
    ) {
        $this->showPage = $showPage;
        $this->indexPage = $indexPage;
        $this->productReviewsIndexPage = $productReviewsIndexPage;
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
            'Product should have name %s, but it has %s'
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
     * @Then I should see the product attribute :attributeName with value :expectedAttribute
     */
    public function iShouldSeeTheProductAttributeWithValue($attributeName, $expectedAttribute)
    {
        $certainAttribute = $this->showPage->getAttributeByName($attributeName);
        Assert::same(
            $certainAttribute,
            $expectedAttribute,
            sprintf(
                'Product should have attribute %s with value %s, but has %s.',
                $attributeName,
                $expectedAttribute,
                $certainAttribute
            )
        );
    }

    /**
     * @Then I should see :count attributes
     */
    public function iShouldSeeAttributes($count)
    {
        $attributes = $this->getProductAttributes();

        Assert::same(
            count($attributes),
            (int) $count,
            'Product should have %2$d attributes, but has %d instead.'
        );
    }

    /**
     * @Then the first attribute should be :name
     */
    public function theFirstAttributeShouldBe($name)
    {
        $attributes = $this->getProductAttributes();
        $firstAttribute = reset($attributes);

        Assert::same(
            $firstAttribute->getText(),
            $name,
            'Expected the first attribute to be %2$s, found %s instead.'
        );
    }

    /**
     * @Then the last attribute should be :name
     */
    public function theLastAttributeShouldBe($name)
    {
        $attributes = $this->getProductAttributes();
        $lastAttribute = end($attributes);

        Assert::same(
            $lastAttribute->getText(),
            $name,
            'Expected the first attribute to be %2$s, found %s instead.'
        );
    }

    /**
     * @When /^I browse products from (taxon "([^"]+)")$/
     */
    public function iCheckListOfProductsForTaxon(TaxonInterface $taxon)
    {
        $this->indexPage->open(['slug' => $taxon->getSlug()]);
    }

    /**
     * @When I search for products with name :name
     */
    public function iSearchForProductsWithName($name)
    {
        $this->indexPage->search($name);
    }

    /**
     * @When I sort products by the lowest price first
     */
    public function iSortProductsByTheLowestPriceFirst()
    {
        $this->indexPage->sort('Cheapest first');
    }

    /**
     * @When I sort products by the highest price first
     */
    public function iSortProductsByTheHighestPriceFisrt()
    {
        $this->indexPage->sort('Most expensive first');
    }

    /**
     * @When I clear filter
     */
    public function iClearFilter()
    {
        $this->indexPage->clearFilter();
    }

    /**
     * @Then I should see the product :productName
     */
    public function iShouldSeeProduct($productName)
    {
        Assert::true(
            $this->indexPage->isProductOnList($productName),
            sprintf("The product %s should appear on page, but it does not.", $productName)
        );
    }

    /**
     * @Then I should not see the product :productName
     */
    public function iShouldNotSeeProduct($productName)
    {
        Assert::false(
            $this->indexPage->isProductOnList($productName),
            sprintf("The product %s should not appear on page, but it does.", $productName)
        );
    }

    /**
     * @Then I should see empty list of products
     */
    public function iShouldSeeEmptyListOfProducts()
    {
        Assert::true(
            $this->indexPage->isEmpty(),
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
            $this->indexPage->isProductWithPriceOnList($productName, $productPrice),
            sprintf("The product %s with price %s should appear on page, but it does not.", $productName, $productPrice)
        );
    }

    /**
     * @Then /^I should be notified that (this product) does not have sufficient stock$/
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product)
    {
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

    /**
     * @When /^I view (oldest|newest) products from (taxon "([^"]+)")$/
     */
    public function iViewSortedProductsFromTaxon($sortDirection, TaxonInterface $taxon)
    {
        $sorting = ['createdAt' => 'oldest' === $sortDirection ? 'asc' : 'desc'];

        $this->indexPage->open(['slug' => $taxon->getSlug(), 'sorting' => $sorting]);
    }

    /**
     * @Then I should see :numberOfProducts products in the list
     */
    public function iShouldSeeProductsInTheList($numberOfProducts)
    {
        $foundRows = $this->indexPage->countProductsItems();

        Assert::same(
            (int) $numberOfProducts,
            $foundRows,
            '%s rows with products should appear on page, %s rows has been found'
        );
    }

    /**
     * @Then I should see a product with name :name
     */
    public function iShouldSeeProductWithName($name)
    {
        Assert::true(
            $this->indexPage->isProductOnPageWithName($name),
            sprintf('The product with name "%s" has not been found.', $name)
        );
    }

    /**
     * @Then the first product on the list should have name :name
     */
    public function theFirstProductOnTheListShouldHaveName($name)
    {
        $actualName = $this->indexPage->getFirstProductNameFromList();

        Assert::same(
            $actualName,
            $name,
            sprintf('Expected first product\'s name to be "%s", but it is "%s".', $name, $actualName)
        );
    }

    /**
     * @Then I should see :count product reviews
     */
    public function iShouldSeeProductReviews($count)
    {
        Assert::same(
            (int) $count,
            $this->showPage->countReviews(),
            'Product has %2$s reviews, but should have %s.'
        );
    }

    /**
     * @Then I should see reviews titled :firstReview, :secondReview and :thirdReview
     */
    public function iShouldSeeReviewsTitled(...$reviews)
    {
        foreach ($reviews as $review) {
            Assert::true(
                $this->showPage->hasReviewTitled($review),
                sprintf('Product should have review titled "%s" but it does not.', $review)
            );
        }
    }

    /**
     * @Then I should not see review titled :title
     */
    public function iShouldNotSeeReviewTitled($title)
    {
        Assert::false(
            $this->showPage->hasReviewTitled($title),
            sprintf('Product should not have review titled "%s" but it does.', $title)
        );
    }

    /**
     * @When /^I check (this product)'s reviews$/
     */
    public function iCheckThisProductSReviews(ProductInterface $product)
    {
        $this->productReviewsIndexPage->open(['slug' => $product->getSlug()]);
    }

    /**
     * @Then /^I should see (\d+) product reviews in the list$/
     */
    public function iShouldSeeNumberOfProductReviewsInTheList($count)
    {
        Assert::same(
            (int) $count,
            $this->productReviewsIndexPage->countReviews(),
            'Product has %2$s reviews in the list, but should have %s.'
        );
    }

    /**
     * @Then I should not see review titled :title in the list
     */
    public function iShouldNotSeeReviewTitledInTheList($title)
    {
        Assert::false(
            $this->productReviewsIndexPage->hasReviewTitled($title),
            sprintf('Product should not have review titled "%s" but it does.', $title)
        );
    }

    /**
     * @Then /^I should be notified that there are no reviews$/
     */
    public function iShouldBeNotifiedThatThereAreNoReviews()
    {
        Assert::true(
            $this->productReviewsIndexPage->hasNoReviewsMessage(),
            'There should be message about no reviews but there is not.'
        );
    }

    /**
     * @Then I should see :rating as its average rating
     */
    public function iShouldSeeAsItsAverageRating($rating)
    {
        $averageRating = $this->showPage->getAverageRating();

        Assert::same(
            (float) $rating,
            $averageRating,
            'Product should have average rating %2$s but has %s.'
        );
    }

    /**
     * @Then /^I should(?:| also) see the product association "([^"]+)" with (products "[^"]+" and "[^"]+")$/
     */
    public function iShouldSeeTheProductAssociationWithProducts($productAssociationName, array $products)
    {
        Assert::true(
            $this->showPage->hasAssociation($productAssociationName),
            sprintf('There should be an association named "%s" but it does not.', $productAssociationName)
        );

        foreach ($products as $product) {
            $this->assertIsProductIsInAssociation($product->getName(), $productAssociationName);
        }
    }

    /**
     * @Then /^average rating of (product "[^"]+") should be (\d+)$/
     */
    public function thisProductAverageRatingShouldBe(ProductInterface $product, $averageRating)
    {
        $this->showPage->tryToOpen(['slug' => $product->getSlug()]);
        $this->iShouldSeeAsItsAverageRating($averageRating);
    }

    /**
     * @Then they should have order like :firstProductName, :secondProductName and :thirdProductName
     */
    public function theyShouldHaveOrderLikeAnd(...$productNames)
    {
        Assert::true(
            $this->indexPage->hasProductsInOrder($productNames),
            'The products have wrong order.'
        );
    }

    /**
     * @param string $productName
     * @param string $productAssociationName
     *
     * @throws \InvalidArgumentException
     */
    private function assertIsProductIsInAssociation($productName, $productAssociationName)
    {
        Assert::true(
            $this->showPage->hasProductInAssociation($productName, $productAssociationName),
            sprintf(
                'There should be an associated product "%s" under association "%s" but it does not.',
                $productName,
                $productAssociationName
            )
        );
    }

    /**
     * @return NodeElement[]
     *
     * @throws \InvalidArgumentException
     */
    private function getProductAttributes()
    {
        $attributes = $this->showPage->getAttributes();
        Assert::notNull($attributes, 'The product has no attributes.');

        return $attributes;
    }
}
