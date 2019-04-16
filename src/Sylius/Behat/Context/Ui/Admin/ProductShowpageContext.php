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
use Sylius\Behat\Element\Product\ShowPage\AssociationsElementInterface;
use Sylius\Behat\Element\Product\ShowPage\AttributesElementInterface;
use Sylius\Behat\Element\Product\ShowPage\DetailsElementInterface;
use Sylius\Behat\Element\Product\ShowPage\MediaElementInterface;
use Sylius\Behat\Element\Product\ShowPage\MoreDetailsElementInterface;
use Sylius\Behat\Element\Product\ShowPage\PricingElementInterface;
use Sylius\Behat\Element\Product\ShowPage\ShippingElementInterface;
use Sylius\Behat\Element\Product\ShowPage\TaxonomyElementIterface;
use Sylius\Behat\Page\Admin\Product\IndexPageInterface;
use Sylius\Behat\Page\Admin\Product\ShowPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class ProductShowpageContext implements Context
{
    /** @var IndexPageInterface */
    private $indexPage;

    /** @var ShowPageInterface */
    private $productShowPage;

    /** @var AssociationsElementInterface */
    private $associationsElement;

    /** @var AttributesElementInterface */
    private $attributesElement;

    /** @var DetailsElementInterface */
    private $detailsElement;

    /** @var MediaElementInterface */
    private $mediaElement;

    /** @var MoreDetailsElementInterface */
    private $moreDetailsElement;

    /** @var PricingElementInterface */
    private $pricingElement;

    /** @var ShippingElementInterface */
    private $shippingElement;

    /** @var TaxonomyElementIterface */
    private $taxonomyElement;

    public function __construct(
        IndexPageInterface $indexPage,
        ShowPageInterface $productShowPage,
        AssociationsElementInterface $associationsElement,
        AttributesElementInterface $attributesElement,
        DetailsElementInterface $detailsElement,
        MediaElementInterface $mediaElement,
        MoreDetailsElementInterface $moreDetailsElement,
        PricingElementInterface $pricingElement,
        ShippingElementInterface $shippingElement,
        TaxonomyElementIterface $taxonomyElement
    ) {
        $this->indexPage = $indexPage;
        $this->productShowPage = $productShowPage;
        $this->associationsElement = $associationsElement;
        $this->attributesElement = $attributesElement;
        $this->detailsElement = $detailsElement;
        $this->mediaElement = $mediaElement;
        $this->moreDetailsElement = $moreDetailsElement;
        $this->pricingElement = $pricingElement;
        $this->shippingElement = $shippingElement;
        $this->taxonomyElement = $taxonomyElement;
    }

    /**
     * @When /^I try to access (this product)'s product page$/
     * @When I access :product product page
     */
    public function iAccessToProductPage(ProductInterface $product): void
    {
        $this->indexPage->showProductPage($product->getName());
    }

    /**
     * @When I browse products
     */
    public function iWantToBrowseProducts(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see this product's product page
     */
    public function iShouldSeeSProductPage(ProductInterface $product): void
    {
        Assert::true($this->productShowPage->isOpen(['id' => $product->getId()]));
    }

    /**
     * @Then I should see product show page without variants
     */
    public function iShouldSeeProductShowPageWithoutVariants(): void
    {
        Assert::true($this->productShowPage->itIsSimpleProductPage());
    }

    /**
     * @Then I should see product name :productName
     */
    public function iShouldSeeProductName(string $productName): void
    {
        Assert::same($productName, $this->productShowPage->getName());
    }

    /**
     * @Then I should see price :price for channel :channelName
     */
    public function iShouldSeePriceForChannel(string $price, string $channelName): void
    {
        Assert::same($this->pricingElement->getPriceForChannel($channelName), $price);
    }

    /**
     * @Then I should see original price :price for channel :channelName
     */
    public function iShouldSeeOrginalPriceForChannel(string $orginalPrice, string $channelName): void
    {
        Assert::same($this->pricingElement->getOrginalPriceForChannel($channelName), $orginalPrice);

    }

    /**
     * @Then I should see product's code :code
     */
    public function iShouldSeeProductSCode(string $code): void
    {
        Assert::same($this->detailsElement->getProductCode(), $code);
    }

    /**
     * @Then I should see product's channels :channel
     */
    public function iShouldSeeProductSChannels(string $channel): void
    {
        Assert::true($this->detailsElement->hasChannel($channel));
    }

    /**
     * @Then I should see current stock of this product :currentStock
     */
    public function iShouldSeeCurrentStockOfThisProduct(int $currentStock): void
    {
        Assert::same($this->detailsElement->getProductCurrentStock(), $currentStock);
    }

    /**
     * @Then I should see product's tax category :taxCategory
     */
    public function iShouldSeeProductSTaxCategory(string $taxCategory): void
    {
        Assert::same($this->detailsElement->getProductTaxCategory(), $taxCategory);
    }

    /**
     * @Then I should see main taxon is :mainTaxonName
     */
    public function iShouldSeeMainTaxonIs(string $mainTaxonName): void
    {
        Assert::same($this->taxonomyElement->getProductMainTaxon(), $mainTaxonName);
    }

    /**
     * @Then I should see product taxon is :productTaxon
     */
    public function iShouldSeeProductTaxon(string $productTaxon): void
    {
        Assert::true($this->taxonomyElement->productHasTaxon($productTaxon));
    }

    /**
     * @Then I should see product's shipping category :shippingCategory
     */
    public function iShouldSeeProductSShippingCategory(string $shippingCategory): void
    {
        Assert::same($this->shippingElement->getProductShippingCategory(), $shippingCategory);
    }

    /**
     * @Then I should see product's width is :width
     */
    public function iShouldSeeProductSWidthIs(float $width): void
    {
        Assert::same($this->shippingElement->getProductWidth(), $width);
    }

    /**
     * @Then I should see product's height is :height
     */
    public function iShouldSeeProductSHeightIs(float $height): void
    {
        Assert::same($this->shippingElement->getProductHeight(), $height);
    }

    /**
     * @Then I should see product's depth is :depth
     */
    public function iShouldSeeProductSDepthIs(float $depth): void
    {
        Assert::same($this->shippingElement->getProductDepth(), $depth);
    }

    /**
     * @Then I should see product's weight is :weight
     */
    public function iShouldSeeProductSWeightIs(float $weight): void
    {
        Assert::same($this->shippingElement->getProductWeight(), $weight);
    }

    /**
     * @Then I should see image
     */
    public function iShouldSeeImageWithPath(): void
    {
        Assert::true($this->mediaElement->isImageDisplayed());
    }

    /**
     * @Then I should see product name is :name
     */
    public function iShouldSeeProductNameIs(string $name): void
    {
        Assert::same($this->moreDetailsElement->getName(), $name);
    }

    /**
     * @Then I should see product slug is :slug
     */
    public function iShouldSeeProductSlugIs(string $slug): void
    {
     Assert::same($this->moreDetailsElement->getProductSlug(), $slug);
    }
    /**
     * @Then I should see product's description is :description
     */
    public function iShouldSeeProductSDescriptionIs(string $description): void
    {
        Assert::same($this->moreDetailsElement->getProductDescription(), $description);
    }

    /**
     * @Then I should see product's meta keywords is :metaKeywords
     */
    public function iShouldSeeProductSMetaKeywordsIs(string $metaKeywords): void
    {
        Assert::same($this->moreDetailsElement->getProductMetaKeywords(), $metaKeywords);
    }

    /**
     * @Then I should see product's short description is :shortDescription
     */
    public function iShouldSeeProductSShortDescriptionIs(string $shortDescription): void
    {
        Assert::same($this->moreDetailsElement->getProductShortDescription(), $shortDescription);
    }

    /**
     * @Then I should see :attribute is :value
     */
    public function iShouldSeeIs(string $attribute, string $value): void
    {
        Assert::same($this->attributesElement->getProductAttribute($attribute), $value);
    }

    /**
     * @Then I should see product association :association with :productName
     */
    public function iShouldSeeProductAssociationWith(string $association, string $productName): void
    {
        Assert::true($this->associationsElement->isProductAssociated($association, $productName));
    }
}
