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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Product\ShowPageInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class BrowsingCatalogPromotionProductVariantsContext implements Context
{
    public function __construct(
        private IndexPageInterface $catalogPromotionProductVariantIndex,
        private ShowPageInterface $productShowPage,
    ) {
    }

    /**
     * @When I browse variants affected by catalog promotion :catalogPromotion
     */
    public function iBrowseVariantsAffectedByCatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->catalogPromotionProductVariantIndex->open(['id' => $catalogPromotion->getId()]);
    }

    /**
     * @When I want to view the product of variant :variant
     */
    public function iWantToViewTheProductOfVariant(ProductVariantInterface $variant): void
    {
        $actions = $this->catalogPromotionProductVariantIndex->getActionsForResource(['code' => $variant->getCode()]);
        $actions->clickLink('Show product');
    }

    /**
     * @Then /^there should be (\d+) product variants? on the list$/
     */
    public function thereShouldBeProductVariantsOnTheList(int $count): void
    {
        Assert::same(
            $this->catalogPromotionProductVariantIndex->countItems(),
            $count,
        );
    }

    /**
     * @Then the product variant :variant should be in the registry
     */
    public function theProductVariantShouldBeInTheRegistry(ProductVariantInterface $variant): void
    {
        Assert::true($this->catalogPromotionProductVariantIndex->isSingleResourceOnPage([
            'code' => $variant->getCode(),
        ]));
    }

    /**
     * @Then I should be viewing the details of product :product
     */
    public function iShouldBeViewingTheDetailsOfProduct(ProductInterface $product): void
    {
        Assert::true($this->productShowPage->isOpen(['id' => $product->getId()]));
    }
}
