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
use Sylius\Behat\Element\Product\IndexPage\VerticalMenuElementInterface;
use Sylius\Behat\Element\Product\ShowPage\LowestPriceInformationElementInterface;
use Sylius\Behat\Page\ErrorPageInterface;
use Sylius\Behat\Page\Shop\Product\IndexPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Behat\Page\Shop\ProductReview\IndexPageInterface as ProductReviewIndexPageInterface;
use Sylius\Behat\Service\Setter\ChannelContextSetterInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    public function __construct(
        private ShowPageInterface $showPage,
        private IndexPageInterface $indexPage,
        private ProductReviewIndexPageInterface $productReviewsIndexPage,
        private ErrorPageInterface $errorPage,
        private VerticalMenuElementInterface $verticalMenuElement,
        private ChannelContextSetterInterface $channelContextSetter,
        private LowestPriceInformationElementInterface $lowestPriceInformationElement,
    ) {
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
     * @When customer view product :product
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
     * @Then /^("[^"]+" variant) and ("[^"]+" variant) should be discounted$/
     * @Then /^("[^"]+" variant) should be discounted$/
     */
    public function variantAndVariantShouldBeDiscounted(ProductVariantInterface ...$variants): void
    {
        /** @var ProductVariantInterface $variant */
        foreach ($variants as $variant) {
            $this->showPage->open(['slug' => $variant->getProduct()->getTranslation('en_US')->getSlug(), '_locale' => 'en_US']);
            $this->showPage->selectVariant($variant->getName());
            Assert::greaterThan($this->showPage->getOriginalPrice(), $this->showPage->getPrice());
        }
    }

    /**
     * @Then /^("[^"]+" variant) and ("[^"]+" variant) should not be discounted$/
     * @Then /^("[^"]+" variant) should not be discounted$/
     */
    public function variantAndVariantShouldNotBeDiscounted(ProductVariantInterface ...$variants): void
    {
        /** @var ProductVariantInterface $variant */
        foreach ($variants as $variant) {
            $this->showPage->open(['slug' => $variant->getProduct()->getTranslation('en_US')->getSlug(), '_locale' => 'en_US']);
            $this->showPage->selectVariant($variant->getName());

            Assert::isEmpty($this->showPage->getOriginalPrice());
        }
    }

    /**
     * @When I try to reach unexistent product
     */
    public function iTryToReachUnexistentProductPage($localeCode = 'en_US'): void
    {
        $this->showPage->tryToOpen([
            'slug' => 'unexisten_product',
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
            ]),
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
     * @Then I should see the product description :description
     */
    public function iShouldSeeTheProductDescription(string $description): void
    {
        Assert::same($this->showPage->getDescription(), $description);
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
     * @When I browse products from taxon :taxon
     * @When I browse products from product taxon code :taxon
     */
    public function iCheckListOfProductsForTaxon(TaxonInterface $taxon): void
    {
        $this->indexPage->open(['slug' => $taxon->getSlug()]);
    }

    /**
     * @When I try to browse products from taxon :taxon with a trailing slash in the path
     */
    public function iTryToBrowseProductsFromTaxonWithATrailingSlashInThePath(TaxonInterface $taxon): void
    {
        $this->indexPage->tryToOpen(['slug' => $taxon->getSlug() . '/']);
    }

    /**
     * @When I search for products with name :name
     */
    public function iSearchForProductsWithName($name): void
    {
        $this->indexPage->search($name);
    }

    /**
     * @When /^I sort products by the (oldest|newest) date first$/
     */
    public function iSortProductsByTheDateFirst(string $sortDirection): void
    {
        $sortDirection = 'oldest' === $sortDirection ? 'Oldest first' : 'Newest first';

        $this->indexPage->sort($sortDirection);
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
    public function iSortProductsByTheHighestPriceFirst(): void
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
     * @Then the product variant price should be :price
     * @Then this product variant price should be :price
     * @Then I should see the product price :price
     * @Then I should see that the combination is :price
     * @Then customer should see the product price :price
     */
    public function iShouldSeeTheProductPrice($price): void
    {
        Assert::same($this->showPage->getPrice(), $price);
    }

    /**
     * @Then the product original price should be :price
     * @Then this product original price should be :price
     * @Then I should see the product original price :price
     * @Then /^customer should see the product original price ("[^"]+")$/
     */
    public function iShouldSeeTheProductOriginalPrice($price): void
    {
        Assert::true($this->showPage->isOriginalPriceVisible());
        Assert::same($this->showPage->getOriginalPrice(), $price);
    }

    /**
     * @Then I should see :productName product discounted from :originalPrice to :price by :promotionLabel on the list
     * @Then I should see :productName product discounted from :originalPrice to :price
     */
    public function iShouldSeeProductDiscountedOnTheList(
        string $productName,
        string $originalPrice,
        string $price,
        ?string $promotionLabel = null,
    ): void {
        Assert::same($this->indexPage->getProductPrice($productName), $price);
        Assert::same($this->indexPage->getProductOriginalPrice($productName), $originalPrice);

        if ($promotionLabel !== null) {
            Assert::same($this->indexPage->getProductPromotionLabel($productName), $promotionLabel);
        }
    }

    /**
     * @Then I should see :productName product not discounted on the list
     */
    public function iShouldSeeProductNotDiscountedOnTheList(string $productName): void
    {
        $originalPrice = $this->indexPage->getProductOriginalPrice($productName);

        Assert::null($originalPrice);
    }

    /**
     * @Then I should see this product is not discounted
     */
    public function iShouldSeeProductIsNotDiscounted(): void
    {
        Assert::null($this->showPage->getOriginalPrice());
    }

    /**
     * @Then /^I should see ("[^"]+" variant) is not discounted$/
     * @Then /^I should see (this variant) is not discounted$/
     */
    public function iShouldSeeVariantIsNotDiscounted(ProductVariantInterface $variant): void
    {
        $this->showPage->selectVariant($variant->getName());

        Assert::null($this->showPage->getOriginalPrice());
    }

    /**
     * @Then I should not see any original price
     * @Then I should see this product has no catalog promotion applied
     */
    public function iShouldNotSeeTheProductOriginalPrice(): void
    {
        Assert::false($this->showPage->isOriginalPriceVisible());
    }

    /**
     * @Then /^the visitor should(?:| still) see "([^"]+)" as the (price|original price) of the ("[^"]+" product) in the ("[^"]+" channel)$/
     */
    public function theVisitorShouldSeeAsThePriceOfTheProductInTheChannel(
        string $price,
        string $priceType,
        ProductInterface $product,
        ChannelInterface $channel,
    ): void {
        $this->channelContextSetter->setChannel($channel);

        $localeCode = $channel->getDefaultLocale()->getCode();
        $this->showPage->open(['slug' => $product->getTranslation($localeCode)->getSlug(), '_locale' => $localeCode]);

        if ($priceType === 'original price') {
            Assert::same($this->showPage->getOriginalPrice(), $price);

            return;
        }

        if ($priceType === 'price') {
            Assert::same($this->showPage->getPrice(), $price);

            return;
        }

        throw new \InvalidArgumentException('Not recognized price type');
    }

    /**
     * @Then the original price of the :product product in the :channel channel should be empty
     */
    public function theOriginalPriceOfTheProductInTheChannelShouldBeEmpty(
        ProductInterface $product,
        ChannelInterface $channel,
    ): void {
        $this->channelContextSetter->setChannel($channel);

        $localeCode = $channel->getDefaultLocale()->getCode();
        $this->showPage->open(['slug' => $product->getTranslation($localeCode)->getSlug(), '_locale' => $localeCode]);

        Assert::null($this->showPage->getOriginalPrice());
    }

    /**
     * @When I select its :optionName as :optionValue
     */
    public function iSelectItsOptionAs(string $optionName, string $optionValue): void
    {
        $this->showPage->selectOption($optionName, $optionValue);
    }

    /**
     * @When I select :variantName variant
     * @When I view :variantName variant
     */
    public function iSelectVariant(string $variantName): void
    {
        $this->showPage->selectVariant($variantName);
    }

    /**
     * @When the visitor view :variant variant
     */
    public function theVisitorViewVariant(ProductVariantInterface $variant): void
    {
        $this->showPage->open(['slug' => $variant->getProduct()->getTranslation('en_US')->getSlug(), '_locale' => 'en_US']);
        $this->showPage->selectVariant($variant->getName());
    }

    /**
     * @When I view :variantName variant of the :product product
     */
    public function iViewVariantOfProduct(string $variantName, ProductInterface $product): void
    {
        $this->showPage->open(['slug' => $product->getTranslation('en_US')->getSlug(), '_locale' => 'en_US']);
        $this->showPage->selectVariant($variantName);
    }

    /**
     * @Then /^I should see ("[^"]+" product) is discounted from "([^"]+)" to "([^"]+)" with "([^"]+)" promotion$/
     * @Then /^I should see (this product) is discounted from "([^"]+)" to "([^"]+)" with "([^"]+)" promotion$/
     * @Then /^I should see (this product) is discounted from "([^"]+)" to "([^"]+)" with "([^"]+)" and "([^"]+)" promotions$/
     * @Then /^I should see (this product) is discounted from "([^"]+)" to "([^"]+)" with "([^"]+)", "([^"]+)" and "([^"]+)" promotions$/
     * @Then /^I should see (this product) is discounted from "([^"]+)" to "([^"]+)" with "([^"]+)", "([^"]+)", "([^"]+)" and "([^"]+)" promotions$/
     */
    public function iShouldSeeProductIsDiscountedFromToWithPromotions(
        ProductInterface $product,
        string $originalPrice,
        string $price,
        string ...$promotionsNames,
    ): void {
        Assert::same($this->showPage->getPrice(), $price);
        Assert::same($this->showPage->getOriginalPrice(), $originalPrice);
        foreach ($promotionsNames as $promotionName) {
            Assert::true(
                $this->showPage->hasCatalogPromotionApplied($promotionName),
                sprintf("Catalog promotion '%s' does not found ", $promotionName),
            );
        }
    }

    /**
     * @Then /^I should see ("[^"]+" variant) is discounted from "([^"]+)" to "([^"]+)" with "([^"]+)" promotion$/
     * @Then /^I should see (this variant) is discounted from "([^"]+)" to "([^"]+)" with "([^"]+)" promotion$/
     * @Then /^I should see (this variant) is discounted from "([^"]+)" to "([^"]+)" with "([^"]+)" and "([^"]+)" promotions$/
     * @Then /^I should see (this variant) is discounted from "([^"]+)" to "([^"]+)" with "([^"]+)", "([^"]+)" and "([^"]+)" promotions$/
     * @Then /^I should see (this variant) is discounted from "([^"]+)" to "([^"]+)" with "([^"]+)", "([^"]+)", "([^"]+)" and "([^"]+)" promotions$/
     */
    public function iShouldSeeVariantIsDiscountedFromToWithPromotions(
        ProductVariantInterface $variant,
        string $originalPrice,
        string $price,
        string ...$promotionsNames,
    ): void {
        $this->showPage->selectVariant($variant->getName());

        Assert::same($this->showPage->getPrice(), $price);
        Assert::same($this->showPage->getOriginalPrice(), $originalPrice);
        foreach ($promotionsNames as $promotionName) {
            Assert::true($this->showPage->hasCatalogPromotionApplied($promotionName));
        }
    }

    /**
     * @Then /^I should see (this variant) is discounted from "([^"]+)" to "([^"]+)" with ([^"]+) promotions$/
     */
    public function iShouldSeeVariantIsDiscountedFromToWithNumberOfPromotions(
        ProductVariantInterface $variant,
        string $originalPrice,
        string $price,
        int $numberOfPromotions,
    ): void {
        $this->showPage->selectVariant($variant->getName());

        Assert::same($this->showPage->getPrice(), $price);
        Assert::same($this->showPage->getOriginalPrice(), $originalPrice);
        Assert::count($this->showPage->getCatalogPromotionNames(), $numberOfPromotions);
    }

    /**
     * @Then /^I should see (this variant) is discounted from "([^"]+)" to "([^"]+)" with only "([^"]+)" promotion$/
     */
    public function iShouldSeeVariantIsDiscountedFromToWithOnlyPromotion(
        ProductVariantInterface $variant,
        string $originalPrice,
        string $price,
        string $promotionName,
    ): void {
        $this->showPage->selectVariant($variant->getName());

        Assert::same(count($this->showPage->getCatalogPromotions()), 1);
        Assert::same($this->showPage->getCatalogPromotionName(), $promotionName);
        Assert::same($this->showPage->getPrice(), $price);
        Assert::same($this->showPage->getOriginalPrice(), $originalPrice);
    }

    /**
     * @Then /^the visitor should(?:| still) see that the ("[^"]+" variant) is discounted from "([^"]+)" to "([^"]+)" with "([^"]+)" promotion$/
     */
    public function theVisitorShouldSeeThatTheVariantIsDiscountedFromToWithPromotion(
        ProductVariantInterface $variant,
        string $originalPrice,
        string $price,
        string $promotionName,
    ): void {
        /** @var ProductInterface $product */
        $product = $variant->getProduct();

        $this->iOpenProductPage($product);
        $this->iShouldSeeVariantIsDiscountedFromToWithPromotions($variant, $originalPrice, $price, $promotionName);
    }

    /**
     * @Then /^the visitor should(?:| still) see that the ("[^"]+" variant) is discounted from "([^"]+)" to "([^"]+)" with ([^"]+) promotions$/
     */
    public function theVisitorShouldSeeVariantIsDiscountedFromToWithNumberOfPromotions(
        ProductVariantInterface $variant,
        string $originalPrice,
        string $price,
        int $numberOfPromotions,
    ): void {
        /** @var ProductInterface $product */
        $product = $variant->getProduct();

        $this->iOpenProductPage($product);

        $this->iShouldSeeVariantIsDiscountedFromToWithNumberOfPromotions($variant, $originalPrice, $price, $numberOfPromotions);
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
                sprintf('Product should have review titled "%s" but it does not.', $review),
            );
        }
    }

    /**
     * @Then I should not see review titled :title
     */
    public function iShouldNotSeeReviewTitled(string $title): void
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
    public function iShouldSeeNumberOfProductReviewsInTheList(int $count): void
    {
        Assert::same($this->productReviewsIndexPage->countReviews(), $count);
    }

    /**
     * @Then I should not see review titled :title in the list
     */
    public function iShouldNotSeeReviewTitledInTheList(string $title): void
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
    public function iShouldSeeTheProductAssociationWithProducts(string $productAssociationName, array $products): void
    {
        Assert::true(
            $this->showPage->hasAssociation($productAssociationName),
            sprintf('There should be an association named "%s" but it does not.', $productAssociationName),
        );

        foreach ($products as $product) {
            $this->assertProductIsInAssociation($product->getName(), $productAssociationName);
        }
    }

    /**
     * @Then /^I should not see the product association "([^"]+)"$/
     */
    public function iShouldNotSeeTheProductAssociationWithProducts(string $productAssociationName): void
    {
        Assert::false(
            $this->showPage->hasAssociation($productAssociationName),
            sprintf('There should not be an association named "%s" but it does.', $productAssociationName),
        );
    }

    /**
     * @Then /^I should(?:| also) see the product association "([^"]+)" with (product "[^"]+")$/
     */
    public function iShouldSeeTheProductAssociationWithProduct(string $productAssociationName, ProductInterface $product): void
    {
        $this->iShouldSeeTheProductAssociationWithProducts($productAssociationName, [$product]);
    }

    /**
     * @Then /^I should(?:| also) not see the product association "([^"]+)" with (product "[^"]+")$/
     */
    public function iShouldNotSeeTheProductAssociationWithProduct(string $productAssociationName, ProductInterface $product): void
    {
        Assert::true(
            $this->showPage->hasAssociation($productAssociationName),
            sprintf('Association "%s" has not been found.', $productAssociationName),
        );

        $this->assertProductIsNotInAssociation($product->getName(), $productAssociationName);
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
     * @Then I should be informed that the product does not exist
     */
    public function iShouldBeInformedThatTheProductDoesNotExist(): void
    {
        Assert::same($this->errorPage->getCode(), 404);
    }

    /**
     * @Then I should be redirected on the product list from taxon :taxon
     */
    public function iShouldBeRedirectedOnTheProductListFromTaxon(TaxonInterface $taxon): void
    {
        $this->indexPage->verify(['slug' => $taxon->getSlug()]);
    }

    /**
     * @Then /^I should be able to select between (\d+) variants$/
     */
    public function iShouldBeAbleToSelectBetweenVariants(int $count): void
    {
        Assert::count($this->showPage->getVariantsNames(), $count);
    }

    /**
     * @Then /^I should not be able to select the ("([^"]*)" variant)$/
     */
    public function iShouldNotBeAbleToSelectTheVariant(ProductVariantInterface $productVariant): void
    {
        Assert::true(!in_array($productVariant->getName(), $this->showPage->getVariantsNames(), true));
    }

    /**
     * @Then /^I should not be able to select the "([^"]*)" ([^\s]+) option value$/
     */
    public function iShouldNotBeAbleToSelectTheOptionValue(string $optionValue, string $optionName)
    {
        Assert::false(in_array($optionValue, $this->showPage->getOptionValues($optionName), true));
    }

    /**
     * @Then /^I should be able to select the "([^"]*)" and "([^"]*)" ([^\s]+) option values$/
     */
    public function iShouldBeAbleToSelectTheAndColorOptionValues(
        string $optionValue1,
        string $optionValue2,
        string $optionName,
    ) {
        Assert::true(in_array($optionValue1, $this->showPage->getOptionValues($optionName), true));
        Assert::true(in_array($optionValue2, $this->showPage->getOptionValues($optionName), true));
    }

    /**
     * @When /^I try to browse products from (taxon "([^"]+)")$/
     */
    public function iTryToBrowseProductsFrom(TaxonInterface $taxon): void
    {
        $this->indexPage->tryToOpen(['slug' => $taxon->getSlug()]);
    }

    /**
     * @Then I should be informed that the taxon does not exist
     */
    public function iShouldBeInformedThatTheTaxonDoesNotExist(): void
    {
        Assert::same($this->errorPage->getCode(), 404);
    }

    /**
     * @Then I should see :firstMenuItem and :secondMenuItem in the vertical menu
     */
    public function iShouldSeeInTheVerticalMenu(string ...$menuItems): void
    {
        Assert::allOneOf($menuItems, $this->verticalMenuElement->getMenuItems());
    }

    /**
     * @Then I should not see :firstMenuItem in the vertical menu
     */
    public function iShouldNotSeeInTheVerticalMenu(string ...$menuItems): void
    {
        $actualMenuItems = $this->verticalMenuElement->getMenuItems();
        foreach ($menuItems as $menuItem) {
            if (in_array($menuItem, $actualMenuItems)) {
                throw new \InvalidArgumentException(sprintf('Vertical menu should not contain %s element', $menuItem));
            }
        }
    }

    /**
     * @Then I should not be able to navigate to parent taxon
     */
    public function iShouldNotBeAbleToNavigateToParentTaxon(): void
    {
        Assert::false($this->verticalMenuElement->canNavigateToParentTaxon());
    }

    /**
     * @Then the visitor should see this variant is not discounted
     */
    public function iShouldSeeThisVariantIsNotDiscounted(): void
    {
        Assert::null($this->showPage->getOriginalPrice());
    }

    /**
     * @Then /^the visitor should see that the ("([^"]*)" variant) is not discounted$/
     */
    public function theVisitorShouldSeeThatTheVariantIsNotDiscounted(ProductVariantInterface $variant): void
    {
        /** @var ProductInterface $product */
        $product = $variant->getProduct();

        $this->iOpenProductPage($product);
        $this->iShouldSeeThisVariantIsNotDiscounted();
    }

    /**
     * @Then I should not be able to click disabled main taxon :taxonName in the breadcrumb
     */
    public function iShouldNotBeAbleToClickDisabledMainTaxonInTheBreacrumb(string $taxonName): void
    {
        Assert::false($this->showPage->hasBreadcrumbLink($taxonName));
    }

    /**
     * @Then /^I should see "([^"]+)" as its lowest price before the discount$/
     */
    public function iShouldSeeAsItsLowestPriceBeforeTheDiscount(string $lowestPriceBeforeDiscount): void
    {
        Assert::true($this->lowestPriceInformationElement->isThereInformationAboutProductLowestPriceWithPrice($lowestPriceBeforeDiscount));
    }

    /**
     * @Then I should not see information about its lowest price
     */
    public function iShouldNotSeeInformationAboutItsLowestPrice(): void
    {
        Assert::false($this->lowestPriceInformationElement->isThereInformationAboutProductLowestPrice());
    }

    /**
     * @param string $productName
     * @param string $productAssociationName
     *
     * @throws \InvalidArgumentException
     */
    private function assertProductIsInAssociation($productName, $productAssociationName): void
    {
        Assert::true(
            $this->showPage->hasProductInAssociation($productName, $productAssociationName),
            sprintf(
                'There should be an associated product "%s" under association "%s" but it does not.',
                $productName,
                $productAssociationName,
            ),
        );
    }

    private function assertProductIsNotInAssociation($productName, $productAssociationName): void
    {
        Assert::false(
            $this->showPage->hasProductInAssociation($productName, $productAssociationName),
            sprintf(
                'Association "%s" should not contain product "%s" but it does.',
                $productName,
                $productAssociationName,
            ),
        );
    }
}
