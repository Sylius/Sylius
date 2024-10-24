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
use Sylius\Behat\Element\Product\ShowPage\AssociationsElementInterface;
use Sylius\Behat\Element\Product\ShowPage\AttributesElementInterface;
use Sylius\Behat\Element\Product\ShowPage\DetailsElementInterface;
use Sylius\Behat\Element\Product\ShowPage\MediaElementInterface;
use Sylius\Behat\Element\Product\ShowPage\OptionsElementInterface;
use Sylius\Behat\Element\Product\ShowPage\PricingElementInterface;
use Sylius\Behat\Element\Product\ShowPage\ShippingElementInterface;
use Sylius\Behat\Element\Product\ShowPage\TaxonomyElementInterface;
use Sylius\Behat\Element\Product\ShowPage\TranslationsElementInterface;
use Sylius\Behat\Element\Product\ShowPage\VariantsElementInterface;
use Sylius\Behat\Page\Admin\Product\IndexPageInterface;
use Sylius\Behat\Page\Admin\Product\ShowPageInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final readonly class ProductShowPageContext implements Context
{
    public function __construct(
        private IndexPageInterface $indexPage,
        private ShowPageInterface $productShowPage,
        private AssociationsElementInterface $associationsElement,
        private AttributesElementInterface $attributesElement,
        private DetailsElementInterface $detailsElement,
        private MediaElementInterface $mediaElement,
        private TranslationsElementInterface $translationsElement,
        private PricingElementInterface $pricingElement,
        private ShippingElementInterface $shippingElement,
        private TaxonomyElementInterface $taxonomyElement,
        private OptionsElementInterface $optionsElement,
        private VariantsElementInterface $variantsElement,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @Given I am browsing products
     */
    public function iAmBrowsingProducts(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I try to reach nonexistent product
     */
    public function iTryToReachNonexistentProductPage(): void
    {
        $this->productShowPage->tryToOpen(['id' => 0, '_locale' => 'en_US']);
    }

    /**
     * @When I access :product product page
     * @When I access the :product product
     */
    public function iAccessTheProduct(ProductInterface $product): void
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
     * @When I access :product product
     */
    public function iAccessProduct(ProductInterface $product): void
    {
        $this->productShowPage->open(['id' => $product->getId()]);
    }

    /**
     * @When I access the price history of a simple product for :channel channel
     */
    public function iAccessThePriceHistoryIndexPageOfSimpleProductForChannel(ChannelInterface $channel): void
    {
        $pricingRow = $this->pricingElement->getSimpleProductPricingRowForChannel($channel->getCode());
        $pricingRow->find('css', '[data-test-price-history]')->click();
    }

    /**
     * @When I access the price history of a product variant :variant for :channel channel
     */
    public function iAccessThePriceHistoryIndexPageOfVariantForChannel(ProductVariantInterface $variant, ChannelInterface $channel): void
    {
        $pricingRow = $this->pricingElement->getVariantPricingRowForChannel($variant->getCode(), $channel->getCode());
        $pricingRow->find('css', '[data-test-price-history]')->click();
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
     * @Then I should see product breadcrumb :breadcrumb
     */
    public function iShouldSeeBreadcrumb(string $breadcrumb): void
    {
        Assert::contains($this->productShowPage->getBreadcrumb(), $breadcrumb);
    }

    /**
     * @Then I should see price :price for channel :channel
     */
    public function iShouldSeePriceForChannel(string $price, ChannelInterface $channel): void
    {
        Assert::same($this->pricingElement->getPriceForChannel($channel->getCode()), $price);
    }

    /**
     * @Then I should see :lowestPriceBeforeDiscount as its lowest price before the discount in :channel channel
     */
    public function iShouldSeeAsItsLowestPriceBeforeTheDiscountInChannel(
        string $lowestPriceBeforeDiscount,
        ChannelInterface $channel,
    ): void {
        Assert::same($this->pricingElement->getLowestPriceBeforeDiscountForChannel($channel->getCode()), $lowestPriceBeforeDiscount);
    }

    /**
     * @Then I should not see the lowest price before the discount in :channel channel
     */
    public function iShouldNotSeeTheLowestPriceBeforeTheDiscountInChannel(ChannelInterface $channel): void
    {
        Assert::same($this->pricingElement->getLowestPriceBeforeDiscountForChannel($channel->getCode()), '-');
    }

    /**
     * @Then I should see the lowest price before the discount of :lowestPriceBeforeDiscount for :variant variant in :channel channel
     */
    public function iShouldSeeVariantWithTheLowestPriceBeforeTheDiscountOfInChannel(
        string $lowestPriceBeforeDiscount,
        ProductVariantInterface $variant,
        ChannelInterface $channel,
    ): void {
        Assert::true($this->variantsElement->hasProductVariantWithLowestPriceBeforeDiscountInChannel(
            $variant->getCode(),
            $lowestPriceBeforeDiscount,
            $channel->getCode(),
        ));
    }

    /**
     * @Then I should not see the lowest price before the discount for :variant variant in :channel channel
     */
    public function iShouldNotSeeTheLowestPriceBeforeTheDiscountForVariantInChannel(
        ProductVariantInterface $variant,
        ChannelInterface $channel,
    ): void {
        Assert::true($this->variantsElement->hasProductVariantWithLowestPriceBeforeDiscountInChannel(
            $variant->getCode(),
            '-',
            $channel->getCode(),
        ));
    }

    /**
     * @Then I should not see price for channel :channelName
     */
    public function iShouldNotSeePriceForChannel(string $channelName): void
    {
        Assert::same($this->pricingElement->getPriceForChannel($channelName), '');
    }

    /**
     * @Then I should see original price :price for channel :channel
     */
    public function iShouldSeeOriginalPriceForChannel(string $originalPrice, ChannelInterface $channel): void
    {
        Assert::same($this->pricingElement->getOriginalPriceForChannel($channel->getCode()), $originalPrice);
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
    public function iShouldSeeProductIsEnabledForChannels(ChannelInterface $channel): void
    {
        Assert::true($this->detailsElement->hasChannel($channel->getCode()));
    }

    /**
     * @Then I should see the product in neither channel
     */
    public function iShouldSeeTheProductInNeitherChannel(): void
    {
        Assert::same($this->detailsElement->countChannels(), 0);
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
     * @Then I should see product taxon :taxonName
     */
    public function iShouldSeeProductTaxon(string $taxonName): void
    {
        Assert::contains($this->taxonomyElement->getProductTaxons(), $taxonName);
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
        Assert::same($this->translationsElement->getName(), $name);
    }

    /**
     * @Then I should see product slug is :slug
     */
    public function iShouldSeeProductSlugIs(string $slug): void
    {
        Assert::same($this->translationsElement->getSlug(), $slug);
    }

    /**
     * @Then I should see product's description is :description
     */
    public function iShouldSeeProductSDescriptionIs(string $description): void
    {
        Assert::same($this->translationsElement->getDescription(), $description);
    }

    /**
     * @Then I should see product's meta keyword(s) is/are :metaKeywords
     */
    public function iShouldSeeProductMetaKeywordsAre(string $metaKeywords): void
    {
        Assert::same($this->translationsElement->getProductMetaKeywords(), $metaKeywords);
    }

    /**
     * @Then I should see product's short description is :shortDescription
     */
    public function iShouldSeeProductShortDescriptionIs(string $shortDescription): void
    {
        Assert::same($this->translationsElement->getShortDescription(), $shortDescription);
    }

    /**
     * @Then I should see product association :association with :productName
     */
    public function iShouldSeeProductAssociationWith(string $association, string $productName): void
    {
        Assert::true($this->associationsElement->isAssociatedWith($association, $productName));
    }

    /**
     * @Then I should see product association type :association
     */
    public function iShouldSeeProductAssociationType(string $association): void
    {
        Assert::true($this->associationsElement->hasAssociation($association));
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
     * @Then I should see :variantName variant with code :code, priced :price and current stock :currentStock and in :channel channel
     */
    public function iShouldSeeVariantWithCodePriceAndCurrentStock(
        string $variantName,
        string $code,
        string $price,
        string $currentStock,
        ChannelInterface $channel,
    ): void {
        Assert::true($this->variantsElement->hasProductVariantWithCodePriceAndCurrentStock(
            $variantName,
            $code,
            $price,
            $currentStock,
            $channel->getCode(),
        ));
    }

    /**
     * @Then I should see the :variant variant
     */
    public function iShouldSeeTheVariant(ProductVariantInterface $variant): void
    {
        Assert::true($this->variantsElement->hasProductVariant($variant->getCode()));
    }

    /**
     * @Then I should see attribute :attribute with value :value in :locale locale
     */
    public function iShouldSeeAttributeWithValueInLocale(string $attribute, string $value, LocaleInterface $locale): void
    {
        Assert::true($this->attributesElement->hasAttributeInLocale($attribute, $locale->getCode(), $value));
    }

    /**
     * @Then /^I should see non-translatable attribute "([^"]+)" with value ([^"]+)%$/
     */
    public function iShouldSeeNonTranslatableAttributeWithValue(string $attribute, string $value): void
    {
        Assert::true($this->attributesElement->hasNonTranslatableAttribute($attribute, (float) $value / 100));
    }

    /**
     * @Then I should not be able to show this product in shop
     */
    public function iShouldNotBeAbleToShowThisProductInShop(): void
    {
        Assert::true($this->productShowPage->isShowInShopButtonDisabled());
    }

    /**
     * @Then this product price should be decreased by catalog promotion :catalogPromotion in :channel channel
     */
    public function thisProductPriceShouldBeDecreasedByCatalogPromotion(
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel,
    ): void {
        Assert::inArray(
            $catalogPromotion->getName(),
            $this->pricingElement->getCatalogPromotionsNamesForChannel($channel->getCode()),
        );

        $url = $this->urlGenerator->generate('sylius_admin_catalog_promotion_show', ['id' => $catalogPromotion->getId()]);
        Assert::inArray($url, $this->pricingElement->getCatalogPromotionLinksForChannel($channel->getCode()));
    }

    /**
     * @Then :variantName variant price should be decreased by catalog promotion :catalogPromotion in :channelName channel
     */
    public function variantPriceShouldBeDecreasedByCatalogPromotion(
        string $variantName,
        CatalogPromotionInterface $catalogPromotion,
        string $channelName,
    ): void {
        Assert::inArray(
            $catalogPromotion->getName(),
            $this->productShowPage->getAppliedCatalogPromotionsNames($variantName, $channelName),
        );

        $url = $this->urlGenerator->generate('sylius_admin_catalog_promotion_show', ['id' => $catalogPromotion->getId()]);
        Assert::inArray($url, $this->productShowPage->getAppliedCatalogPromotionsLinks($variantName, $channelName));
    }

    /**
     * @Then :variantName variant price should not be decreased by catalog promotion :catalogPromotion in :channelName channel
     */
    public function variantPriceShouldNotBeDecreasedByCatalogPromotion(
        string $variantName,
        CatalogPromotionInterface $catalogPromotion,
        string $channelName,
    ): void {
        $appliedPromotions = $this->productShowPage->getAppliedCatalogPromotionsNames($variantName, $channelName);
        Assert::false(in_array($catalogPromotion->getName(), $appliedPromotions));
    }
}
