<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Shop\Product\IndexPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Behat\Page\Shop\ProductReview\IndexPageInterface as ProductReviewIndexPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

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
    public function iShouldBeAbleToAccessProduct(ProductInterface $product): void
    {
        $this->showPage->tryToOpen(['slug' => $product->getSlug()]);

        Assert::true($this->showPage->isOpen(['slug' => $product->getSlug()]));
    }

    /**
     * @Then I should not be able to access product :product
     */
    public function iShouldNotBeAbleToAccessProduct(ProductInterface $product): void
    {
        $this->showPage->tryToOpen(['slug' => $product->getSlug()]);

        Assert::false($this->showPage->isOpen(['slug' => $product->getSlug()]));
    }

    /**
     * @When /^I check (this product)'s details$/
     * @When /^I check (this product)'s details in the ("([^"]+)" locale)$/
     * @When I view product :product
     * @When I view product :product in the :localeCode locale
     */
    public function iOpenProductPage(ProductInterface $product, $localeCode = 'en_US'): void
    {
        $this->showPage->open(['slug' => $product->getTranslation($localeCode)->getSlug(), '_locale' => $localeCode]);
    }

    /**
     * @When /^I try to check (this product)'s details in the ("([^"]+)" locale)$/
     */
    public function iTryToOpenProductPage(ProductInterface $product, $localeCode = 'en_US'): void
    {
        $this->showPage->tryToOpen([
            'slug' => $product->getTranslation($localeCode)->getSlug(),
            '_locale' => $localeCode,
        ]);
    }

    /**
     * @Then /^I should not be able to view (this product) in the ("([^"]+)" locale)$/
     */
    public function iShouldNotBeAbleToViewThisProductInLocale(ProductInterface $product, $localeCode = 'en_US'): void
    {
        Assert::false(
            $this->showPage->isOpen([
                'slug' => $product->getTranslation($localeCode)->getSlug(),
                '_locale' => $localeCode,
            ])
        );
    }

    /**
     * @Then I should see the product name :name
     */
    public function iShouldSeeProductName($name): void
    {
        Assert::same($this->showPage->getName(), $name);
    }

    /**
     * @When I open page :url
     */
    public function iOpenPage($url): void
    {
        $this->showPage->visit($url);
    }

    /**
     * @Then I should be on :product product detailed page
     * @Then I should still be on product :product page
     */
    public function iShouldBeOnProductDetailedPage(ProductInterface $product): void
    {
        Assert::true($this->showPage->isOpen(['slug' => $product->getSlug()]));
    }

    /**
     * @Then I should (also) see the product attribute :attributeName with value :expectedAttribute
     */
    public function iShouldSeeTheProductAttributeWithValue($attributeName, $expectedAttribute): void
    {
        Assert::same($this->showPage->getAttributeByName($attributeName), $expectedAttribute);
    }

    /**
     * @Then I should not see the product attribute :attributeName
     */
    public function iShouldNotSeeTheProductAttribute(string $attributeName): void
    {
        $this->showPage->getAttributeByName($attributeName);
    }

    /**
     * @Then I should (also) see the product attribute :attributeName with date :expectedAttribute
     */
    public function iShouldSeeTheProductAttributeWithDate($attributeName, $expectedAttribute): void
    {
        Assert::eq(
            new \DateTime($this->showPage->getAttributeByName($attributeName)),
            new \DateTime($expectedAttribute)
        );
    }

    /**
     * @Then I should see :count attributes
     */
    public function iShouldSeeAttributes($count): void
    {
        Assert::same(count($this->getProductAttributes()), (int) $count);
    }

    /**
     * @Then the first attribute should be :name
     */
    public function theFirstAttributeShouldBe($name): void
    {
        $attributes = $this->getProductAttributes();

        Assert::same(reset($attributes)->getText(), $name);
    }

    /**
     * @Then the last attribute should be :name
     */
    public function theLastAttributeShouldBe($name): void
    {
        $attributes = $this->getProductAttributes();

        Assert::same(end($attributes)->getText(), $name);
    }

    /**
     * @When /^I browse products from (taxon "([^"]+)")$/
     */
    public function iCheckListOfProductsForTaxon(TaxonInterface $taxon): void
    {
        $this->indexPage->open(['slug' => $taxon->getSlug()]);
    }

    /**
     * @When I search for products with name :name
     */
    public function iSearchForProductsWithName($name): void
    {
        $this->indexPage->search($name);
    }

    /**
     * @When I sort products by the lowest price first
     */
    public function iSortProductsByTheLowestPriceFirst(): void
    {
        $this->indexPage->sort('Cheapest first');
    }

    /**
     * @When I sort products by the highest price first
     */
    public function iSortProductsByTheHighestPriceFisrt(): void
    {
        $this->indexPage->sort('Most expensive first');
    }

    /**
     * @When I sort products alphabetically from a to z
     */
    public function iSortProductsAlphabeticallyFromAToZ(): void
    {
        $this->indexPage->sort('From A to Z');
    }

    /**
     * @When I sort products alphabetically from z to a
     */
    public function iSortProductsAlphabeticallyFromZToA(): void
    {
        $this->indexPage->sort('From Z to A');
    }

    /**
     * @When I clear filter
     */
    public function iClearFilter(): void
    {
        $this->indexPage->clearFilter();
    }

    /**
     * @Then I should see the product :productName
     */
    public function iShouldSeeProduct($productName): void
    {
        Assert::true($this->indexPage->isProductOnList($productName));
    }

    /**
     * @Then I should not see the product :productName
     */
    public function iShouldNotSeeProduct($productName): void
    {
        Assert::false($this->indexPage->isProductOnList($productName));
    }

    /**
     * @Then I should see empty list of products
     */
    public function iShouldSeeEmptyListOfProducts(): void
    {
        Assert::true($this->indexPage->isEmpty());
    }

    /**
     * @Then I should see that it is out of stock
     */
    public function iShouldSeeItIsOutOfStock(): void
    {
        Assert::true($this->showPage->isOutOfStock());
    }

    /**
     * @Then I should be unable to add it to the cart
     */
    public function iShouldBeUnableToAddItToTheCart(): void
    {
        Assert::false($this->showPage->hasAddToCartButton());
    }

    /**
     * @Then the product price should be :price
     * @Then I should see the product price :price
     */
    public function iShouldSeeTheProductPrice($price): void
    {
        Assert::same($this->showPage->getPrice(), $price);
    }

    /**
     * @When I set its :optionName to :optionValue
     */
    public function iSetItsOptionTo($optionName, $optionValue): void
    {
        $this->showPage->selectOption($optionName, $optionValue);
    }

    /**
     * @When I select :variantName variant
     */
    public function iSelectVariant($variantName): void
    {
        $this->showPage->selectVariant($variantName);
    }

    /**
     * @Then its current variant should be named :name
     */
    public function itsCurrentVariantShouldBeNamed($name): void
    {
        Assert::same($this->showPage->getCurrentVariantName(), $name);
    }

    /**
     * @Then I should see the product :productName with price :productPrice
     */
    public function iShouldSeeTheProductWithPrice($productName, $productPrice): void
    {
        Assert::same($this->indexPage->getProductPrice($productName), $productPrice);
    }

    /**
     * @Then /^I should be notified that (this product) does not have sufficient stock$/
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product): void
    {
        Assert::true($this->showPage->hasProductOutOfStockValidationMessage($product));
    }

    /**
     * @Then /^I should not be notified that (this product) does not have sufficient stock$/
     */
    public function iShouldNotBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product): void
    {
        Assert::false($this->showPage->hasProductOutOfStockValidationMessage($product));
    }

    /**
     * @Then I should see a main image
     */
    public function iShouldSeeAMainImage(): void
    {
        Assert::true($this->showPage->isMainImageDisplayed());
    }

    /**
     * @When /^I view (oldest|newest) products from (taxon "([^"]+)")$/
     */
    public function iViewSortedProductsFromTaxon($sortDirection, TaxonInterface $taxon): void
    {
        $sorting = ['createdAt' => 'oldest' === $sortDirection ? 'asc' : 'desc'];

        $this->indexPage->open(['slug' => $taxon->getSlug(), 'sorting' => $sorting]);
    }

    /**
     * @Then I should see :numberOfProducts products in the list
     */
    public function iShouldSeeProductsInTheList($numberOfProducts): void
    {
        Assert::same($this->indexPage->countProductsItems(), (int) $numberOfProducts);
    }

    /**
     * @Then I should see a product with name :name
     */
    public function iShouldSeeProductWithName($name): void
    {
        Assert::true($this->indexPage->isProductOnPageWithName($name));
    }

    /**
     * @Then the first product on the list should have name :name
     */
    public function theFirstProductOnTheListShouldHaveName($name): void
    {
        Assert::same($this->indexPage->getFirstProductNameFromList(), $name);
    }

    /**
     * @Then the first product on the list should have name :name and price :price
     */
    public function theFirstProductOnTheListShouldHaveNameAndPrice($name, $price): void
    {
        Assert::same($this->indexPage->getFirstProductNameFromList(), $name);
        Assert::same($this->indexPage->getProductPrice($name), $price);
    }

    /**
     * @Then the last product on the list should have name :name
     */
    public function theLastProductOnTheListShouldHaveName($name): void
    {
        Assert::same($this->indexPage->getLastProductNameFromList(), $name);
    }

    /**
     * @Then the last product on the list should have name :name and price :price
     */
    public function theLastProductOnTheListShouldHaveNameAndPrice($name, $price): void
    {
        Assert::same($this->indexPage->getLastProductNameFromList(), $name);
        Assert::same($this->indexPage->getProductPrice($name), $price);
    }

    /**
     * @Then I should see :count product reviews
     */
    public function iShouldSeeProductReviews($count): void
    {
        Assert::same($this->showPage->countReviews(), (int) $count);
    }

    /**
     * @Then I should see reviews titled :firstReview, :secondReview and :thirdReview
     */
    public function iShouldSeeReviewsTitled(...$reviews): void
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
    public function iShouldNotSeeReviewTitled($title): void
    {
        Assert::false($this->showPage->hasReviewTitled($title));
    }

    /**
     * @When /^I check (this product)'s reviews$/
     */
    public function iCheckThisProductSReviews(ProductInterface $product): void
    {
        $this->productReviewsIndexPage->open(['slug' => $product->getSlug()]);
    }

    /**
     * @Then /^I should see (\d+) product reviews in the list$/
     */
    public function iShouldSeeNumberOfProductReviewsInTheList($count): void
    {
        Assert::same($this->productReviewsIndexPage->countReviews(), (int) $count);
    }

    /**
     * @Then I should not see review titled :title in the list
     */
    public function iShouldNotSeeReviewTitledInTheList($title): void
    {
        Assert::false($this->productReviewsIndexPage->hasReviewTitled($title));
    }

    /**
     * @Then /^I should be notified that there are no reviews$/
     */
    public function iShouldBeNotifiedThatThereAreNoReviews(): void
    {
        Assert::true($this->productReviewsIndexPage->hasNoReviewsMessage());
    }

    /**
     * @Then I should see :rating as its average rating
     */
    public function iShouldSeeAsItsAverageRating($rating): void
    {
        Assert::same($this->showPage->getAverageRating(), (float) $rating);
    }

    /**
     * @Then /^I should(?:| also) see the product association "([^"]+)" with (products "[^"]+" and "[^"]+")$/
     */
    public function iShouldSeeTheProductAssociationWithProducts($productAssociationName, array $products): void
    {
        Assert::true(
            $this->showPage->hasAssociation($productAssociationName),
            sprintf('There should be an association named "%s" but it does not.', $productAssociationName)
        );

        foreach ($products as $product) {
            $this->assertProductIsInAssociation($product->getName(), $productAssociationName);
        }
    }

    /**
     * @Then /^average rating of (product "[^"]+") should be (\d+)$/
     */
    public function thisProductAverageRatingShouldBe(ProductInterface $product, $averageRating): void
    {
        $this->showPage->tryToOpen(['slug' => $product->getSlug()]);
        $this->iShouldSeeAsItsAverageRating($averageRating);
    }

    /**
     * @Then they should have order like :firstProductName, :secondProductName and :thirdProductName
     */
    public function theyShouldHaveOrderLikeAnd(...$productNames): void
    {
        Assert::true($this->indexPage->hasProductsInOrder($productNames));
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function assertProductIsInAssociation(string $productName, string $productAssociationName): void
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
