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
use Sylius\Behat\Context\Ui\Admin\Helper\ProductTypeEnum;
use Sylius\Behat\Page\Admin\Product\CreateConfigurableProductPageInterface;
use Sylius\Behat\Page\Admin\Product\CreateSimpleProductPageInterface;
use Sylius\Behat\Page\Admin\Product\ShowPageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\UpdatePageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class NavigatingBetweenProductShowAndEditPagesContext implements Context
{
    private ProductTypeEnum $currentProductType;

    public function __construct(
        private readonly UpdateSimpleProductPageInterface $updateSimpleProductPage,
        private readonly UpdatePageInterface $updateVariantProductPage,
        private readonly ShowPageInterface $productShowPage,
        private readonly CreateSimpleProductPageInterface $createSimpleProductPage,
        private readonly CreateConfigurableProductPageInterface $createConfigurableProductPage,
    ) {
    }

    /**
     * @When I access the :product product
     */
    public function iAccessTheProduct(ProductInterface $product): void
    {
        $this->productShowPage->open(['id' => $product->getId()]);
    }

    /**
     * @When I go to edit page
     */
    public function iGoToEditPage(): void
    {
        $this->productShowPage->switchToEditPage();
    }

    /**
     * @When I go to show page
     */
    public function iGoToShowPage(): void
    {
        $this->updateSimpleProductPage->switchToShowPage();
    }

    /**
     * @When I go to edit page of :variant variant
     */
    public function iGoToEditPageOfVariant(ProductVariantInterface $variant): void
    {
        $this->productShowPage->showVariantEditPage($variant);
    }

    /**
     * @When /^I want to create a new (simple|configurable) product$/
     */
    public function iWantToCreateANewProduct(string $productType): void
    {
        $productType = ProductTypeEnum::from($productType);

        match ($productType) {
            ProductTypeEnum::simple => $this->createSimpleProductPage->open(),
            ProductTypeEnum::configurable => $this->createConfigurableProductPage->open(),
        };

        $this->currentProductType = $productType;
    }

    /**
     * @When I want to modify the :product product
     */
    public function iWantToModifyAProduct(ProductInterface $product): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);
    }

    /**
     * @When I change position of the :image image to :position
     */
    public function iChangePositionOfTheImageTo(string $image, int $position): void
    {
        $this->updateSimpleProductPage->changeImagePosition($image, $position);
    }

    /**
     * @Then I should be on :product product edit page
     */
    public function iShouldBeOnProductEditPage(ProductInterface $product): void
    {
        Assert::true($this->updateSimpleProductPage->isOpen(['id' => $product->getId()]));
    }

    /**
     * @Then I should be on :variant variant edit page
     */
    public function iShouldBeOnVariantEditPage(ProductVariantInterface $variant): void
    {
        Assert::true($this->updateVariantProductPage->isOpen(['productId' => $variant->getProduct()->getId(), 'id' => $variant->getId()]));
    }

    /**
     * @Then I should be on :product product show page
     */
    public function iShouldBeOnProductShowPage(ProductInterface $product): void
    {
        Assert::true($this->productShowPage->isOpen(['id' => $product->getId()]));
    }

    /**
     * @Then I should not be able to open the product show page
     */
    public function iShouldNotBeAbleToAccessTheProductShowPage(): void
    {
        match (ProductTypeEnum::from($this->currentProductType->value)) {
            ProductTypeEnum::simple => Assert::false($this->updateSimpleProductPage->hasShowPageButton()),
            ProductTypeEnum::configurable => Assert::false($this->createConfigurableProductPage->hasShowPageButton()),
        };
    }
}
