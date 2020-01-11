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
use Sylius\Behat\Element\Product\ShowPage\OptionsElementInterface;
use Sylius\Behat\Element\Product\ShowPage\PricingElementInterface;
use Sylius\Behat\Element\Product\ShowPage\ShippingElementInterface;
use Sylius\Behat\Element\Product\ShowPage\TaxonomyElementInterface;
use Sylius\Behat\Element\Product\ShowPage\VariantsElementInterface;
use Sylius\Behat\Page\Admin\Product\IndexPageInterface;
use Sylius\Behat\Page\Admin\Product\ShowPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class ProductShowPageContext implements Context
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

    /** @var TaxonomyElementInterface */
    private $taxonomyElement;

    /** @var OptionsElementInterface */
    private $optionsElement;

    /** @var VariantsElementInterface */
    private $variantsElement;

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
        TaxonomyElementInterface $taxonomyElement,
        OptionsElementInterface $optionsElement,
        VariantsElementInterface $variantsElement
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
        $this->optionsElement = $optionsElement;
        $this->variantsElement = $variantsElement;
    }

    /**
     * @Given I am browsing products
     */
    public function iAmBrowsingProducts(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I access :product product page
     */
    public function iAccessProductPage(ProductInterface $product): void
    {
        $this->indexPage->showProductPage($product->getName());
    }

    /**
     * @When I show this product in the :channel channel
     */
    public function iShowThisProductInTheChannel(string $channel): void
    {
        $this->productShowPage->showProductInChannel($channel);
    }

    /**
     * @When I show this product in this channel
     */
    public function iShowThisProductInThisChannel(): void
    {
        $this->productShowPage->showProductInSingleChannel();
    }

    /**
     * @When I go to edit page
     */
    public function iGoToEditPage(): void
    {
        $this->productShowPage->showProductEditPage();
    }

    /**
     * @When I go to edit page of :variant variant
     */
    public function iGoToEditPageOfVariant(ProductVariantInterface $variant): void
    {
        $this->productShowPage->showVariantEditPage($variant);
    }

    /**
     * @Then I should see this product's product page
     */
    public function iShouldSeeThisProductPage(ProductInterface $product): void
    {
        Assert::true($this->productShowPage->isOpen(['id' => $product->getId()]));
    }

    /**
     * @Then I should see product show page without variants
     */
    public function iShouldSeeProductShowPageWithoutVariants(): void
    {
        Assert::true($this->productShowPage->isSimpleProductPage());
    }

    /**
     * @Then I should see product show page with variants
     */
    public function iShouldSeeProductShowPageWithVariants(): void
    {
        Assert::false($this->productShowPage->isSimpleProductPage());
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
        Assert::same($this->pricingElement->getOriginalPriceForChannel($channelName), $orginalPrice);
    }

    /**
     * @Then I should see product's code is :code
     */
    public function iShouldSeeProductCodeIs(string $code): void
    {
        Assert::same($this->detailsElement->getProductCode(), $code);
    }

    /**
     * @Then I should see the product is enabled for channel :channel
     */
    public function iShouldSeeProductIsEnabledForChannels(string $channel): void
    {
        Assert::true($this->detailsElement->hasChannel($channel));
    }

    /**
     * @Then I should see :currentStock as a current stock of this product
     */
    public function iShouldSeeAsACurrentStockOfThisProduct(int $currentStock): void
    {
        Assert::same($this->detailsElement->getProductCurrentStock(), $currentStock);
    }

    /**
     * @Then I should see product's tax category is :taxCategory
     */
    public function iShouldSeeProductTaxCategoryIs(string $taxCategory): void
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
     * @Then I should see product taxon is :taxonName
     */
    public function iShouldSeeProductTaxonIs(string $taxonName): void
    {
        Assert::true($this->taxonomyElement->hasProductTaxon($taxonName));
    }

    /**
     * @Then I should see product's shipping category is :shippingCategory
     */
    public function iShouldSeeProductShippingCategoryIs(string $shippingCategory): void
    {
        Assert::same($this->shippingElement->getProductShippingCategory(), $shippingCategory);
    }

    /**
     * @Then I should see product's width is :width
     */
    public function iShouldSeeProductWidthIs(float $width): void
    {
        Assert::same($this->shippingElement->getProductWidth(), $width);
    }

    /**
     * @Then I should see product's height is :height
     */
    public function iShouldSeeProductHeightIs(float $height): void
    {
        Assert::same($this->shippingElement->getProductHeight(), $height);
    }

    /**
     * @Then I should see product's depth is :depth
     */
    public function iShouldSeeProductDepthIs(float $depth): void
    {
        Assert::same($this->shippingElement->getProductDepth(), $depth);
    }

    /**
     * @Then I should see product's weight is :weight
     */
    public function iShouldSeeProductWeightIs(float $weight): void
    {
        Assert::same($this->shippingElement->getProductWeight(), $weight);
    }

    /**
     * @Then I should see an image related to this product
     */
    public function iShouldSeeImageRelatedToThisProduct(): void
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
        Assert::same($this->moreDetailsElement->getSlug(), $slug);
    }

    /**
     * @Then I should see product's description is :description
     */
    public function iShouldSeeProductSDescriptionIs(string $description): void
    {
        Assert::same($this->moreDetailsElement->getDescription(), $description);
    }

    /**
     * @Then I should see product's meta keyword(s) is/are :metaKeywords
     */
    public function iShouldSeeProductMetaKeywordsAre(string $metaKeywords): void
    {
        Assert::same($this->moreDetailsElement->getProductMetaKeywords(), $metaKeywords);
    }

    /**
     * @Then I should see product's short description is :shortDescription
     */
    public function iShouldSeeProductShortDescriptionIs(string $shortDescription): void
    {
        Assert::same($this->moreDetailsElement->getShortDescription(), $shortDescription);
    }

    /**
     * @Then I should see product association :association with :productName
     */
    public function iShouldSeeProductAssociationWith(string $association, string $productName): void
    {
        Assert::true($this->associationsElement->isProductAssociated($association, $productName));
    }

    /**
     * @Then I should see option :optionName
     */
    public function iShouldSeeOption(string $optionName): void
    {
        Assert::true($this->optionsElement->isOptionDefined($optionName));
    }

    /**
     * @Then I should see :count variants
     */
    public function iShouldSeeVariants(int $count): void
    {
        Assert::same($this->variantsElement->countVariantsOnPage(), $count);
    }

    /**
     * @Then I should see :variantName variant with code :code, priced :price and current stock :currentStock
     */
    public function iShouldSeeVariantWithCodePriceAndCurrentStock(string $variantName, string $code, string $price, string $currentStock): void
    {
        Assert::true($this->variantsElement->hasProductVariantWithCodePriceAndCurrentStock($variantName, $code, $price, $currentStock));
    }

    /**
     * @Then I should see attribute :attribute with value :value in :locale locale
     */
    public function iShouldSeeAttributeWithValueInLocale(string $attribute, string $value, string $locale): void
    {
        Assert::true($this->attributesElement->hasAttributeInLocale($attribute, $locale, $value));
    }

    /**
     * @Then I should not be able to show this product in shop
     */
    public function iShouldNotBeAbleToShowThisProductInShop(): void
    {
        Assert::true($this->productShowPage->isShowInShopButtonDisabled());
    }
}
