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
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Page\Shop\Cart\SummaryPageInterface;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\JQueryHelper;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private SummaryPageInterface $summaryPage,
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

        $this->waitForCartSummary();
    }

    public function addToCartWithQuantity(string $quantity): void
    {
        $this->getElement('quantity')->setValue($quantity);
        $this->getElement('add_to_cart_button')->click();

        $this->waitForCartSummary();
    }

    public function addToCartWithVariant(string $variant): void
    {
        $this->selectVariant($variant);

        $this->getElement('add_to_cart_button')->click();

        $this->waitForCartSummary();
    }

    public function addToCartWithOption(ProductOptionInterface $option, string $optionValue): void
    {
        $select = $this->getElement('option_select', ['%optionCode%' => $option->getCode()]);

        $this->getDocument()->selectFieldOption($select->getAttribute('name'), $optionValue);
        $this->getElement('add_to_cart_button')->click();

        $this->waitForCartSummary();
    }

    public function getAttributeByName(string $name): ?string
    {
        $attributesTable = $this->getElement('attributes');

        if (DriverHelper::isJavascript($this->getDriver())) {
            try {
                $attributesTab = $this->getElement('tab', ['%name%' => 'attributes']);
                if (!$attributesTab->hasAttribute('[data-test-active]')) {
                    $attributesTab->click();
                }
            } catch (ElementNotFoundException) {
                return null;
            }
        }

        $nameTd = $attributesTable->find('css', sprintf('[data-test-product-attribute-name="%s"]', $name));

        if (null === $nameTd) {
            return null;
        }

        $row = $nameTd->getParent();
        $text = $row->find('css', '[data-test-product-attribute-value]')->getText();

        return str_replace(["\u{200B}", "\u{200A}", "\u{202F}"], ' ', trim($text));
    }

    public function getAttributeListByName(string $name): array
    {
        $attribute = $this->getAttributeByName($name);

        return explode(', ', $attribute);
    }

    public function getAttributes(): array
    {
        $attributesTable = $this->getElement('attributes');

        return $attributesTable->findAll('css', '[data-test-product-attribute-name]');
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
        $catalogPromotions = $this->getDocument()->findAll('css', '.column .promotion_label');
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
        foreach ($this->getElement('product_box')->findAll('css', '.promotion_label') as $catalogPromotion) {
            $catalogPromotions[] = explode(' - ', $catalogPromotion->getText())[0];
        }

        return $catalogPromotions;
    }

    public function getCatalogPromotionNames(): array
    {
        $productPriceContent = $this->getElement('product_box');
        $catalogPromotions = $productPriceContent->findAll('css', '.promotion_label');

        return array_map(fn (NodeElement $element): string => $element->getText(), $catalogPromotions);
    }

    public function getCurrentUrl(): string
    {
        return $this->getDriver()->getCurrentUrl();
    }

    public function getCurrentVariantName(): string
    {
        $currentVariantRow = $this->getElement('current_variant_input')->getParent()->getParent();

        return $currentVariantRow->find('css', 'td:first-child')->getText();
    }

    public function getName(): string
    {
        return $this->getElement('product_name')->getText();
    }

    public function getPrice(): string
    {
        return $this->getElement('product_price')->getText();
    }

    public function getOriginalPrice(): ?string
    {
        $originalPrice = $this->getElement('product_original_price');

        if (
            $originalPrice->getAttribute('style') !== null &&
            str_contains($originalPrice->getAttribute('style'), 'display: none')
        ) {
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

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool
    {
        $message = sprintf('%s does not have sufficient stock.', $product->getName());

        if (!$this->hasElement('validation_errors')) {
            return false;
        }

        return $this->getElement('validation_errors')->getText() === $message;
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

    public function isMainImageDisplayed(): bool
    {
        if (!$this->hasElement('main_image')) {
            return false;
        }

        $imageUrl = $this->getElement('main_image')->getAttribute('src');
        $this->getDriver()->visit($imageUrl);
        $pageText = $this->getDocument()->getText();
        $this->getDriver()->back();

        return false === stripos($pageText, '404 Not Found');
    }

    public function countReviews(): int
    {
        return count($this->getElement('reviews')->findAll('css', '[data-test-title]'));
    }

    public function selectOption(string $optionCode, string $optionValue): void
    {
        $optionElement = $this->getElement('option_select', ['%optionCode%' => strtoupper($optionCode)]);
        $optionElement->selectOption($optionValue);
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

            return;
        }

        $this->getDocument()->fillField($variantRadio->getAttribute('name'), $variantRadio->getAttribute('value'));
    }

    public function visit($url): void
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
            } catch (UnexpectedPageException) {
                $isOpen = false;
                sleep(1);
            }
        } while (!$isOpen && microtime(true) < $end);

        if (!$isOpen) {
            throw new UnexpectedPageException('Is not open: ' . $e->getMessage() . ' ' . json_encode($urlParameters));
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
        return $this->getDocument()->findAll('css', '[data-tab="details"]')[1]->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_to_cart_button' => '[data-test-add-to-cart-button]',
            'applied_promotions' => '#appliedPromotions',
            'association' => '[data-test-product-association="%associationName%"]',
            'attributes' => '[data-test-product-attributes]',
            'average_rating' => '[data-test-average-rating]',
            'breadcrumb' => '.breadcrumb',
            'catalog_promotion' => '.promotion_label',
            'current_variant_input' => '[data-test-product-variants] td input:checked',
            'details' => '[data-tab="details"]',
            'main_image' => '[data-test-main-image]',
            'name' => '[data-test-product-name]',
            'option_select' => '#sylius_add_to_cart_cartItem_variant_%optionCode%',
            'out_of_stock' => '[data-test-product-out-of-stock]',
            'product_box' => '[data-test-product-box]',
            'product_name' => '[data-test-product-name]',
            'product_original_price' => '[data-test-product-price-content] [data-test-product-original-price]',
            'product_price' => '[data-test-product-price-content] [data-test-product-price]',
            'product_price_content' => '[data-test-product-price-content]',
            'quantity' => '[data-test-quantity]',
            'reviews' => '[data-test-product-reviews]',
            'reviews_title' => '[data-test-title="%title%"]',
            'selecting_variants' => '[data-test-product-selecting-variant]',
            'tab' => '[data-test-tab="%name%"]',
            'validation_errors' => '[data-test-cart-validation-error]',
            'variant_radio' => '[data-test-product-variants] tbody tr:contains("%variantName%") input',
            'variants_rows' => '[data-test-product-variants-row]',
        ]);
    }

    private function waitForCartSummary(): void
    {
        if (DriverHelper::isJavascript($this->getDriver())) {
            JQueryHelper::waitForAsynchronousActionsToFinish($this->getSession());
            $this->getDocument()->waitFor(3, fn (): bool => $this->summaryPage->isOpen());
        }
    }

    public function hasBreadcrumbLink(string $taxonName): bool
    {
        return $this->getElement('breadcrumb')->findLink($taxonName) != null;
    }
}
