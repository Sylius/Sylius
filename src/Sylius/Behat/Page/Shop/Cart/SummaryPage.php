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

namespace Sylius\Behat\Page\Shop\Cart;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

class SummaryPage extends SymfonyPage implements SummaryPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_cart_summary';
    }

    public function getGrandTotal(): string
    {
        Assert::true(false); // Tracks which methods can be removed after completing cart tasks
        $totalElement = $this->getElement('grand_total');

        return $totalElement->getText();
    }

    public function getBaseGrandTotal(): string
    {
        Assert::true(false);
        $totalElement = $this->getElement('base_grand_total');

        return $totalElement->getText();
    }

    public function getIncludedTaxTotal(): string
    {
        Assert::true(false);
        $includedTaxTotalElement = $this->getElement('tax_included');

        return $includedTaxTotalElement->getText();
    }

    public function getExcludedTaxTotal(): string
    {
        Assert::true(false);
        $excludedTaxTotalElement = $this->getElement('tax_excluded');

        return $excludedTaxTotalElement->getText();
    }

    public function areTaxesCharged(): bool
    {
        Assert::true(false);

        try {
            $this->getElement('no_taxes');
        } catch (ElementNotFoundException) {
            return true;
        }

        return false;
    }

    public function getShippingTotal(): string
    {
        return $this->getElement('shipping_total')->getText();
    }

    public function hasShippingTotal(): bool
    {
        return $this->hasElement('shipping_total');
    }

    public function getPromotionTotal(): string
    {
        Assert::true(false);
        $shippingTotalElement = $this->getElement('promotion_total');

        return $shippingTotalElement->getText();
    }

    public function getItemsTotal(): string
    {
        return $this->getElement('items_total')->getText();
    }

    public function getItemTotal(string $productName): string
    {
        Assert::true(false);
        $itemTotalElement = $this->getElement('product_total', ['%name%' => $productName]);

        return $itemTotalElement->getText();
    }

    public function getItemUnitRegularPrice(string $productName): string
    {
        return $this->getElement('item_unit_regular_price', ['%name%' => $productName])->getText();
    }

    public function getItemUnitPrice(string $productName): string
    {
        return $this->getElement('item_unit_price', ['%name%' => $productName])->getText();
    }

    public function hasOriginalPrice(string $productName): bool
    {
        return $this->hasElement('item_unit_regular_price', ['%name%' => $productName]);
    }

    public function getItemImage(int $itemNumber): string
    {
        return $this->getElement('item_image', ['%number%' => $itemNumber - 1])->getAttribute('src');
    }

    public function isItemDiscounted(string $productName): bool
    {
        Assert::true(false);

        return $this->hasElement('product_unit_regular_price', ['%name%' => $productName]);
    }

    public function removeProduct(string $productName): void
    {
        $this->getElement('remove_item', ['%name%' => $productName])->press();
        $this->waitForComponentsUpdate();
    }

    public function applyCoupon(string $couponCode): void
    {
        Assert::true(false);
        $this->getElement('coupon_field')->setValue($couponCode);
        $this->getElement('apply_coupon_button')->press();
    }

    public function changeQuantity(string $productName, string $quantity): void
    {
        $this->getElement('item_quantity', ['%name%' => $productName])->setValue($quantity);
        $this->waitForComponentsUpdate();
    }

    public function specifyQuantity(string $productName, int $quantity): void
    {
        Assert::true(false);
        $this->getElement('item_quantity_input', ['%name%' => $productName])->setValue($quantity);
    }

    public function countOrderItems(): int
    {
        return count($this->getElement('cart_items')->findAll('css', '[data-test-cart-item]'));
    }

    public function hasItemNamed(string $name): bool
    {
        return $this->hasElement('cart_item', ['%name%' => $name]);
    }

    public function hasItemWithVariantNamed(string $variantName): bool
    {
        $cartItems = $this->getElement('cart_items');
        foreach ($cartItems->findAll('css', '[data-test-product-variant-name]') as $elementVariantName) {
            if ($variantName === $elementVariantName->getText()) {
                return true;
            }
        }

        return false;
    }

    public function getItemOptionValue(string $productName, string $optionName): string
    {
        return $this->getElement('item_product_option_value', ['%name%' => $productName, '%option_name%' => $optionName])->getText();
    }

    public function hasItemWithCode(string $code): bool
    {
        return $this->hasElement('item_product_variant_code', ['%code%' => $code]);
    }

    public function hasItemWithInsufficientStock(string $productName): bool
    {
        Assert::true(false);
        $product = $this->getElement('product_row', ['%name%' => $productName]);

        return str_contains($product->getText(), 'Insufficient stock');
    }

    public function isEmpty(): bool
    {
        return str_contains($this->getElement('flash_message')->getText(), 'Your cart is empty');
    }

    public function getQuantity(string $productName): int
    {
        return (int) $this->getElement('item_quantity', ['%name%' => $productName])->getValue();
    }

    public function getCartTotal(): string
    {
        $cartTotalText = $this->getElement('cart_total')->getText();

        if (str_contains($cartTotalText, ',')) {
            return strstr($cartTotalText, ',', true);
        }

        return trim($cartTotalText);
    }

    public function clearCart(): void
    {
        $this->getElement('clear_cart')->click();
        $this->waitForComponentsUpdate();
    }

    public function updateCart(): void
    {
        Assert::true(false);
        $this->getElement('update_button')->click();
    }

    public function checkout(): void
    {
        $this->getElement('checkout_button')->click();
    }

    public function waitForRedirect(int $timeout): void
    {
        $this->getDocument()->waitFor($timeout, fn () => $this->isOpen());
    }

    /** @param array<string, string> $parameters */
    public function getValidationMessage(string $element, array $parameters = []): string
    {
        $foundElement = $this->getFieldElement($element, $parameters);

        $validationMessage = $foundElement->find('css', '.invalid-feedback');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $validationMessage->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
//            'apply_coupon_button' => '[data-test-apply-coupon-button]',
//            'base_grand_total' => '[data-test-cart-base-grand-total]',
            'cart_items' => '[data-test-cart-items]',
            'cart_item' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"]',
            'cart_total' => '[data-test-cart-total]',
            'checkout_button' => '[data-test-cart-checkout-button]',
            'clear_cart' => '[data-test-clear-cart]',
//            'coupon_field' => '[data-test-cart-promotion-coupon-input]',
            'flash_message' => '[data-test-sylius-flash-message]',
            'form' => '[data-live-name-value="sylius_shop:cart:form"]',
            'summary_component' => '[data-live-name-value="sylius_shop:checkout:summary"]',
//            'grand_total' => '[data-test-cart-grand-total]',
            'item_image' => '[data-test-cart-items] [data-test-cart-item="%number%"] [data-test-cart-item-product] [data-test-main-image]',
            'item_product_option_value' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"] [data-test-cart-item-product] [data-test-option-name="%option_name%"] [data-test-option-value]',
            'item_product_variant_code' => '[data-test-cart-items] [data-test-cart-item-product] [data-test-product-variant-code="%code%"]',
            'item_quantity' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"] [data-test-cart-item-quantity]',
            'item_unit_price' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"] [data-test-cart-item-unit-price]',
            'item_unit_regular_price' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"] [data-test-cart-item-unit-regular-price]',
            'items_total' => '[data-test-cart-items-total]',
//            'no_taxes' => '[data-test-cart-no-tax]',
//            'product_row' => '[data-test-cart-product-row="%name%"]',
//            'product_total' => '[data-test-cart-product-row="%name%"] [data-test-cart-product-subtotal]',
//            'promotion_total' => '[data-test-cart-promotion-total]',
            'remove_item' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"] [data-test-remove-cart-item]',
//            'save_button' => '[data-test-apply-coupon-button]',
            'shipping_total' => '[data-test-cart-shipping-total]',
//            'tax_excluded' => '[data-test-cart-tax-exluded]',
//            'tax_included' => '[data-test-cart-tax-included]',
//            'update_button' => '[data-test-cart-update-button]',
        ]);
    }

//    /**
//     * @throws ElementNotFoundException
//     */
//    private function hasItemWith(string $attributeName, array|string $selector): bool
//    {
//        $itemsAttributes = $this->getElement('cart_items')->findAll('css', $selector);
//
//        foreach ($itemsAttributes as $itemAttribute) {
//            if ($attributeName === $itemAttribute->getText()) {
//                return true;
//            }
//        }
//
//        return false;
//    }
//
//    private function getPriceFromString(string $price): int
//    {
//        return (int) round((float) str_replace(['€', '£', '$'], '', $price) * 100, 2);
//    }

    private function waitForComponentsUpdate(): void
    {
        $form = $this->getElement('form');
        usleep(500000); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $form->waitFor(1500, fn () => !$form->hasAttribute('busy'));

        try {
            $summaryComponent = $this->getElement('summary_component');
            usleep(500000); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
            $summaryComponent->waitFor(1500, fn () => !$summaryComponent->hasAttribute('busy'));
        } catch (ElementNotFoundException) {
            return;
        }
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
