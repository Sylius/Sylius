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

namespace Sylius\Behat\Page\Shop\Product;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Page\Shop\Cart\SummaryPageInterface;
use Sylius\Behat\Page\Shop\Page as ShopPage;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class ShowPage extends ShopPage implements ShowPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private readonly SummaryPageInterface $summaryPage,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_product_show';
    }

    public function addToCart(): void
    {
        $this->getElement('add_to_cart_button')->click();

        $this->waitForElementToBeReady();
    }

    public function addToCartWithQuantity(string $quantity): void
    {
        $this->getElement('quantity')->setValue($quantity);
        $this->waitForElementUpdate('add_to_cart_component');

        $this->getElement('add_to_cart_button')->click();
        $this->waitForElementToBeReady();
    }

    public function addToCartWithVariant(string $variant): void
    {
        $this->selectVariant($variant);

        $this->getElement('add_to_cart_button')->click();

        $this->waitForElementToBeReady();
    }

    public function addToCartWithOption(ProductOptionInterface $option, string $optionValue): void
    {
        $select = $this->getElement('option_select', ['%optionCode%' => $option->getCode()]);

        $this->getDocument()->selectFieldOption($select->getAttribute('name'), $optionValue);
        $this->getElement('add_to_cart_button')->click();

        $this->waitForElementToBeReady();
    }

    public function getAttributeByName(string $name): ?string
    {
        try {
            $attributeValueElement = $this->getElement('attributes')
                ->find('css', sprintf('[data-test-product-attribute-value="%s"]', $name));
        } catch (ElementNotFoundException) {
            return null;
        }

        return $attributeValueElement->getText();
    }

    public function getAttributeListByName(string $name): array
    {
        $attribute = $this->getAttributeByName($name);

        return explode(', ', $attribute);
    }

    public function getAttributes(): array
    {
        return $this->getElement('attributes')
            ->findAll('css', '[data-test-product-attribute-name]');
    }

    public function getAverageRating(): float
    {
        return (float) $this->getElement('average_rating')->getAttribute('data-test-average-rating');
    }

    public function getCatalogPromotionName(): string
    {
        return explode(' - ', $this->getElement('catalog_promotion')->getText())[0];
    }

    public function hasCatalogPromotionApplied(string $name): bool
    {
        $catalogPromotions = $this->getDocument()->findAll('css', '[data-test-promotion-label]');
        foreach ($catalogPromotions as $catalogPromotion) {
            if (explode(' - ', $catalogPromotion->getText())[0] === $name) {
                return true;
            }
        }

        return false;
    }

    public function getCatalogPromotions(): array
    {
        $catalogPromotions = [];

        /** @var NodeElement $catalogPromotion */
        foreach ($this->getElement('product_box')->findAll('css', '[data-test-promotion-label]') as $catalogPromotion) {
            $catalogPromotions[] = explode(' - ', $catalogPromotion->getText())[0];
        }

        return $catalogPromotions;
    }

    public function getCatalogPromotionNames(): array
    {
        $catalogPromotions = $this->getElement('applied_catalog_promotions')->findAll('css', '[data-test-applied-catalog-promotion]');

        return array_map(fn (NodeElement $element): string => $element->getText(), $catalogPromotions);
    }

    public function getCurrentUrl(): string
    {
        return $this->getDriver()->getCurrentUrl();
    }

    public function getCurrentVariantName(): string
    {
        $currentVariantRow = $this->getElement('current_variant_input')->getParent()->getParent()->getParent();

        return $currentVariantRow->find('css', 'td:first-child')->getText();
    }

    public function getName(): string
    {
        return $this->getElement('product_name')->getText();
    }

    public function getPrice(): string
    {
        $this->waitForElementToBeReady();

        return $this->getElement('product_price')->getText();
    }

    public function getOriginalPrice(): ?string
    {
        try {
            $originalPrice = $this->getElement('product_original_price');
        } catch (ElementNotFoundException) {
            return null;
        }

        return $originalPrice->getText();
    }

    public function isOriginalPriceVisible(): bool
    {
        try {
            return null !== $this->getElement('product_original_price')->find('css', 'del');
        } catch (ElementNotFoundException) {
            return false;
        }
    }

    public function hasAddToCartButton(): bool
    {
        if (!$this->hasElement('add_to_cart_button')) {
            return false;
        }

        return $this->getElement('add_to_cart_button') !== null && false === $this->getElement('add_to_cart_button')->hasAttribute('disabled');
    }

    public function hasAssociation(string $productAssociationName): bool
    {
        try {
            $this->getElement('association', ['%associationName%' => $productAssociationName]);
        } catch (ElementNotFoundException) {
            return false;
        }

        return true;
    }

    public function hasProductInAssociation(string $productName, string $productAssociationName): bool
    {
        $products = $this->getElement('association', ['%associationName%' => $productAssociationName]);

        Assert::notNull($products);

        return $productName === $products->find('css', sprintf('[data-test-product-name="%s"]', $productName))?->getText();
    }

    public function hasReviewTitled(string $title): bool
    {
        try {
            $element = $this->getElement('reviews_title', ['%title%' => $title]);
        } catch (ElementNotFoundException) {
            return false;
        }

        return $title === $element->getAttribute('data-test-title');
    }

    public function isOutOfStock(): bool
    {
        return $this->hasElement('out_of_stock');
    }

    public function isMainImageOfType(string $type): bool
    {
        $mainImage = $this->getElement('main_image', ['%type%' => $type]);

        return $mainImage !== null;
    }

    public function isMainImageOfTypeDisplayed(string $type): bool
    {
        if (!$this->hasElement('main_image', ['%type%' => $type])) {
            return false;
        }

        $imageUrl = $this->getElement('main_image', ['%type%' => $type])->getAttribute('src');
        $this->getDriver()->visit($imageUrl);
        $pageText = $this->getDocument()->getText();
        $this->getDriver()->back();

        return false === stripos($pageText, '404 Not Found');
    }

    public function getFirstThumbnailsImageType(): string
    {
        $thumbnails = $this->getElement('thumbnails');
        $images = $thumbnails->findAll('css', 'img');

        return $images[0]->getAttribute('data-test-thumbnail-image');
    }

    public function getSecondThumbnailsImageType(): string
    {
        $thumbnails = $this->getElement('thumbnails');
        $images = $thumbnails->findAll('css', 'img');

        return $images[1]->getAttribute('data-test-thumbnail-image');
    }

    public function countReviews(): int
    {
        return count($this->getElement('reviews')->findAll('css', '[data-test-title]'));
    }

    public function selectOption(string $optionCode, string $optionValue): void
    {
        $optionElement = $this->getElement('option_select', ['%optionCode%' => strtoupper($optionCode)]);
        $optionElement->selectOption($optionValue);
        $this->waitForElementToBeReady();
    }

    public function selectVariant(string $variantName): void
    {
        try {
            $variantRadio = $this->getElement('variant_radio', ['%variantName%' => $variantName]);
        } catch (ElementNotFoundException) {
            return;
        }

        if (DriverHelper::isJavascript($this->getDriver())) {
            $variantRadio->click();
            $this->waitForElementToBeReady();

            return;
        }

        $this->getDocument()->fillField($variantRadio->getAttribute('name'), $variantRadio->getAttribute('value'));
    }

    public function visit(string $url): void
    {
        $absoluteUrl = $this->makePathAbsolute($url);
        $this->getDriver()->visit($absoluteUrl);
    }

    public function open(array $urlParameters = []): void
    {
        $start = microtime(true);
        $end = $start + 5;
        do {
            try {
                parent::open($urlParameters);
                $isOpen = true;
            } catch (UnexpectedPageException $e) {
                $isOpen = false;
                sleep(1);
            }
        } while (!$isOpen && microtime(true) < $end);

        if (!$isOpen) {
            $exceptionMessage = isset($e) ? $e->getMessage() : 'Waited 5 seconds for page to open';

            throw new UnexpectedPageException('Is not open: ' . $exceptionMessage . ' ' . json_encode($urlParameters));
        }
    }

    public function getVariantsNames(): array
    {
        $variantsNames = [];
        /** @var NodeElement $variantRow */
        foreach ($this->getElement('variants_rows')->findAll('css', 'td:first-child') as $variantRow) {
            $variantsNames[] = $variantRow->getText();
        }

        return $variantsNames;
    }

    public function getOptionValues(string $optionCode): array
    {
        $optionElement = $this->getElement('option_select', ['%optionCode%' => strtoupper($optionCode)]);

        return array_map(
            fn (NodeElement $element) => $element->getText(),
            $optionElement->findAll('css', 'option'),
        );
    }

    public function getDescription(): string
    {
        return $this->getElement('details')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_to_cart_button' => '[data-test-button="add-to-cart-button"]',
            'add_to_cart_component' => '[data-live-name-value="sylius_shop:product:add_to_cart_form"]',
            'applied_catalog_promotions' => '[data-test-applied-catalog-promotions]',
            'association' => '[data-test-product-association="%associationName%"]',
            'attributes' => '[data-test-product-attributes]',
            'average_rating' => '[data-test-average-rating]',
            'breadcrumb' => '.breadcrumb',
            'catalog_promotion' => '[data-test-promotion-label]',
            'current_variant_input' => '[data-test-product-variants] td input:checked',
            'details' => '[data-test-product-details]',
            'main_image' => '[data-test-main-image="%type%"]',
            'name' => '[data-test-product-name]',
            'option_select' => '#sylius_shop_add_to_cart_cartItem_variant_%optionCode%',
            'out_of_stock' => '[data-test-product-out-of-stock]',
            'product_box' => '[data-test-product-box]',
            'product_name' => '[data-test-product-name]',
            'product_original_price' => '[data-test-product-box] [data-test-product-original-price]',
            'product_price' => '[data-test-product-price]',
            'quantity' => '[data-test-quantity]',
            'reviews' => '[data-test-product-reviews]',
            'reviews_title' => '[data-test-title="%title%"]',
            'tab' => '[data-test-tab="%name%"]',
            'thumbnail_image' => '[data-test-thumbnail-image="%type%"]',
            'thumbnails' => '[data-test-thumbnails]',
            'variant_radio' => '[data-test-product-variants] tbody tr:contains("%variantName%") input',
            'variants_rows' => '[data-test-product-variants-row]',
        ]);
    }

    private function waitForElementToBeReady(): void
    {
        if (DriverHelper::isJavascript($this->getDriver())) {
            $this->getDocument()->waitFor(1, fn (): bool => $this->summaryPage->isOpen());
        }
    }

    public function hasBreadcrumbLink(string $taxonName): bool
    {
        return $this->getElement('breadcrumb')->findLink($taxonName) != null;
    }

    /**
     * @param array<string, string> $parameters
     *
     * @throws ElementNotFoundException
     */
    private function getFieldElement(string $element, array $parameters): NodeElement
    {
        $element = $this->getElement($element, $parameters);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
