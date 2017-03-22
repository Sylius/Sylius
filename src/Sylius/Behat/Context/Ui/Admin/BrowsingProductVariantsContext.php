<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\ProductVariant\IndexPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class BrowsingProductVariantsContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var ProductVariantResolverInterface
     */
    private $defaultProductVariantResolver;

    /**
     * @param IndexPageInterface $indexPage
     * @param ProductVariantResolverInterface $defaultProductVariantResolver
     */
    public function __construct(
        IndexPageInterface $indexPage,
        ProductVariantResolverInterface $defaultProductVariantResolver
    ) {
        $this->indexPage = $indexPage;
        $this->defaultProductVariantResolver = $defaultProductVariantResolver;
    }

    /**
     * @When I start sorting variants by :field
     */
    public function iSortProductsBy($field)
    {
        $this->indexPage->sortBy($field);
    }

    /**
     * @Then the :productVariantCode variant of the :product product should appear in the store
     */
    public function theProductVariantShouldAppearInTheShop($productVariantCode, ProductInterface $product)
    {
        $this->indexPage->open(['productId' => $product->getId()]);

        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $productVariantCode]));
    }

    /**
     * @Then the :productVariantCode variant of the :product product should not appear in the store
     */
    public function theProductVariantShouldNotAppearInTheShop($productVariantCode, ProductInterface $product)
    {
        $this->indexPage->open(['productId' => $product->getId()]);

        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $productVariantCode]));
    }

    /**
     * @Then the :product product should have no variants
     */
    public function theProductShouldHaveNoVariants(ProductInterface $product)
    {
        $this->indexPage->open(['productId' => $product->getId()]);

        $this->assertNumberOfVariantsOnProductPage(0);
    }

    /**
     * @Then the :product product should have only one variant
     */
    public function theProductShouldHaveOnlyOneVariant(ProductInterface $product)
    {
        $this->indexPage->open(['productId' => $product->getId()]);

        $this->assertNumberOfVariantsOnProductPage(1);
    }

    /**
     * @When /^I (?:|want to )view all variants of (this product)$/
     * @When /^I view(?:| all) variants of the (product "[^"]+")$/
     */
    public function iWantToViewAllVariantsOfThisProduct(ProductInterface $product)
    {
        $this->indexPage->open(['productId' => $product->getId()]);
    }

    /**
     * @Then I should see :numberOfProductVariants variants in the list
     * @Then I should see :numberOfProductVariants variant in the list
     * @Then I should not see any variants in the list
     */
    public function iShouldSeeProductVariantsInTheList($numberOfProductVariants = 0)
    {
        Assert::same($this->indexPage->countItems(), (int) $numberOfProductVariants);
    }

    /**
     * @Then /^(this variant) should not exist in the product catalog$/
     */
    public function productVariantShouldNotExist(ProductVariantInterface $productVariant)
    {
        $this->indexPage->open(['productId' => $productVariant->getProduct()->getId()]);

        Assert::false($this->indexPage->isSingleResourceOnPage(['name' => $productVariant->getName()]));
    }

    /**
     * @Then /^(this variant) should still exist in the product catalog$/
     */
    public function productShouldExistInTheProductCatalog(ProductVariantInterface $productVariant)
    {
        $this->theProductVariantShouldAppearInTheShop($productVariant->getCode(), $productVariant->getProduct());
    }

    /**
     * @Then /^the variant "([^"]+)" should have (\d+) items on hand$/
     */
    public function thisVariantShouldHaveItemsOnHand($productVariantName, $quantity)
    {
        Assert::true($this->indexPage->isSingleResourceWithSpecificElementOnPage(
            ['name' => $productVariantName],
            sprintf('td > div.ui.label:contains("%s")', $quantity)
        ));
    }

    /**
     * @Then /^the "([^"]+)" variant of ("[^"]+" product) should have (\d+) items on hand$/
     */
    public function theVariantOfProductShouldHaveItemsOnHand($productVariantName, ProductInterface $product, $quantity)
    {
        $this->indexPage->open(['productId' => $product->getId()]);

        Assert::true($this->indexPage->isSingleResourceWithSpecificElementOnPage(
            ['name' => $productVariantName],
            sprintf('td > div.ui.label:contains("%s")', $quantity)
        ));
    }

    /**
     * @Then /^I should see that the ("([^"]+)" variant) is not tracked$/
     */
    public function iShouldSeeThatIsNotTracked(ProductVariantInterface $productVariant)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage([
            'name' => $productVariant->getName(),
            'inventory' => 'Not tracked',
        ]));
    }

    /**
     * @Then /^I should see that the ("[^"]+" variant) has zero on hand quantity$/
     */
    public function iShouldSeeThatTheVariantHasZeroOnHandQuantity(ProductVariantInterface $productVariant)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage([
            'name' => $productVariant->getName(),
            'inventory' => '0 Available on hand',
        ]));
    }

    /**
     * @Then /^(\d+) units of (this product) should be on hold$/
     */
    public function unitsOfThisProductShouldBeOnHold($quantity, ProductInterface $product)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->defaultProductVariantResolver->getVariant($product);

        $this->assertOnHoldQuantityOfVariant($quantity, $variant);
    }

    /**
     * @Then /^(\d+) units of (this product) should be on hand$/
     */
    public function unitsOfThisProductShouldBeOnHand($quantity, ProductInterface $product)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->defaultProductVariantResolver->getVariant($product);

        Assert::same($this->indexPage->getOnHandQuantityFor($variant), (int) $quantity);
    }

    /**
     * @Then /^there should be no units of (this product) on hold$/
     */
    public function thereShouldBeNoUnitsOfThisProductOnHold(ProductInterface $product)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->defaultProductVariantResolver->getVariant($product);

        $this->assertOnHoldQuantityOfVariant(0, $variant);
    }

    /**
     * @Then the :variant variant should have :amount items on hold
     */
    public function thisVariantShouldHaveItemsOnHold(ProductVariantInterface $variant, $amount)
    {
        $this->assertOnHoldQuantityOfVariant((int) $amount, $variant);
    }

    /**
     * @Then the :variant variant of :product product should have :amount items on hold
     */
    public function theVariantOfProductShouldHaveItemsOnHold(ProductVariantInterface $variant, ProductInterface $product, $amount)
    {
        $this->indexPage->open(['productId' => $product->getId()]);

        $this->assertOnHoldQuantityOfVariant((int) $amount, $variant);
    }

    /**
     * @Then the first variant in the list should have :field :value
     */
    public function theFirstVariantInTheListShouldHave($field, $value)
    {
        Assert::same($this->indexPage->getColumnFields($field)[0], $value);
    }

    /**
     * @Then the last variant in the list should have :field :value
     */
    public function theLastVariantInTheListShouldHave($field, $value)
    {
        $values = $this->indexPage->getColumnFields($field);

        Assert::same(end($values), $value);
    }

    /**
     * @Then /^(this variant) should have a (\d+) item currently in stock$/
     */
    public function thisVariantShouldHaveAItemCurrentlyInStock(ProductVariantInterface $productVariant, $amountInStock)
    {
        $this->indexPage->open(['productId' => $productVariant->getProduct()->getId()]);

        Assert::same($this->indexPage->getOnHandQuantityFor($productVariant), (int) $amountInStock);
    }

    /**
     * @param int $expectedAmount
     * @param ProductVariantInterface $variant
     *
     * @throws \InvalidArgumentException
     */
    private function assertOnHoldQuantityOfVariant($expectedAmount, $variant)
    {
        $actualAmount = $this->indexPage->getOnHoldQuantityFor($variant);

        Assert::same(
            $actualAmount,
            (int) $expectedAmount,
            sprintf(
                'Unexpected on hold quantity for "%s" variant. It should be "%s" but is "%s"',
                $variant->getName(),
                $expectedAmount,
                $actualAmount
            )
        );
    }

    /**
     * @param int $amount
     */
    private function assertNumberOfVariantsOnProductPage($amount)
    {
        Assert::same((int) $this->indexPage->countItems(), $amount, 'Product has %d variants, but should have %d');
    }
}
