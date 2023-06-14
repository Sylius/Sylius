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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\CatalogPromotion\ProductVariant\IndexPageInterface;
use Sylius\Behat\Page\Admin\Product\ShowPageInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class BrowsingCatalogPromotionProductVariantsContext implements Context
{
    public function __construct(
        private IndexPageInterface $catalogPromotionProductVariantIndexPage,
        private ShowPageInterface $productShowPage,
    ) {
    }

    /**
     * @When I browse variants affected by catalog promotion :catalogPromotion
     */
    public function iBrowseVariantsAffectedByCatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->catalogPromotionProductVariantIndexPage->open(['id' => $catalogPromotion->getId()]);
    }

    /**
     * @When I want to view the product of variant :variant
     */
    public function iWantToViewTheProductOfVariant(ProductVariantInterface $variant): void
    {
        $this->catalogPromotionProductVariantIndexPage->showProductOf($variant->getCode());
    }

    /**
     * @Then /^there should be (\d+) product variants? on the list$/
     */
    public function thereShouldBeProductVariantsOnTheList(int $count): void
    {
        Assert::same(
            $this->catalogPromotionProductVariantIndexPage->countItems(),
            $count,
        );
    }

    /**
     * @Then it should be the :variantName product variant
     * @Then it should be :firstVariant and :secondVariant product variants
     */
    public function theProductVariantShouldBeInTheRegistry(string ...$variantsNames): void
    {
        foreach ($variantsNames as $variantName) {
            Assert::true($this->catalogPromotionProductVariantIndexPage->isSingleResourceOnPage([
                'name' => $variantName,
            ]));
        }
    }

    /**
     * @Then I should be viewing the details of product :product
     */
    public function iShouldBeViewingTheDetailsOfProduct(ProductInterface $product): void
    {
        Assert::true($this->productShowPage->isOpen(['id' => $product->getId()]));
    }
}
