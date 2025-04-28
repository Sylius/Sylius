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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Shop\Page as ShopPage;
use Sylius\Component\Core\Model\ProductInterface;

class SummaryPage extends ShopPage implements SummaryPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_cart_summary';
    }

    public function getGrandTotal(): string
    {
        return $this->getElement('grand_total')->getText();
    }

    public function getBaseGrandTotal(): string
    {
        return $this->getElement('base_grand_total')->getText();
    }

    public function getIncludedTaxTotal(): string
    {
        return $this->getElement('tax_included')->getText();
    }

    public function getExcludedTaxTotal(): string
    {
        return $this->getElement('tax_excluded')->getText();
    }

    public function areTaxesCharged(): bool
    {
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
        return $this->getElement('promotion_total')->getText();
    }

    public function getItemsTotal(): string
    {
        return $this->getElement('items_total')->getText();
    }

    public function getItemTotal(string $productName): string
    {
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
        return $this->hasElement('item_unit_regular_price', ['%name%' => $productName]);
    }

    public function removeProduct(string $productName): void
    {
        $this->getElement('remove_item', ['%name%' => $productName])->press();
        $this->waitForComponentsUpdate();
    }

    public function applyCoupon(string $couponCode): void
    {
        $this->getElement('coupon_field')->setValue($couponCode);
        $this->getElement('apply_coupon_button')->press();
        $this->waitForComponentsUpdate();
    }

    public function removeCoupon(): void
    {
        $this->getElement('remove_coupon')->press();
        $this->waitForComponentsUpdate();
    }

    public function changeQuantity(string $productName, string $quantity): void
    {
        $this->getElement('item_quantity', ['%name%' => $productName])->setValue($quantity);
        $this->waitForComponentsUpdate();
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
        $product = $this->getElement('product_row', ['%name%' => $productName]);

        return str_contains($product->getText(), 'Insufficient stock');
    }

    public function cartIsEmpty(): bool
    {
        return str_contains($this->getElement('flash_message_info')->getText(), 'Your cart is empty');
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

    public function checkout(): void
    {
        $this->getElement('checkout_button')->click();
    }

    public function waitForRedirect(int $timeout): void
    {
        $this->getDocument()->waitFor($timeout, fn () => $this->isOpen());
    }

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool
    {
        $message = sprintf('%s does not have sufficient stock.', $product->getName());

        return $this->hasElement('validation_errors') && $this->getElement('validation_errors')->getText() === $message;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'apply_coupon_button' => '[data-test-apply-coupon-button]',
            'base_grand_total' => '[data-test-cart-base-grand-total]',
            'cart_items' => '[data-test-cart-items]',
            'cart_item' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"]',
            'cart_total' => '[data-test-cart-total]',
            'checkout_button' => '[data-test-cart-checkout-button]',
            'clear_cart' => '[data-test-clear-cart]',
            'coupon_field' => '[data-test-cart-promotion-coupon-input]',
            'flash_message_info' => '[data-test-sylius-flash-message="alert-info"]',
            'form' => '[data-live-name-value="sylius_shop:cart:form"]',
            'summary_component' => '[data-live-name-value="sylius_shop:cart:summary"]',
            'grand_total' => '[data-test-cart-grand-total]',
            'item_image' => '[data-test-cart-items] [data-test-cart-item="%number%"] [data-test-cart-item-product] [data-test-main-image]',
            'item_product_option_value' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"] [data-test-cart-item-product] [data-test-option-name="%option_name%"] [data-test-option-value]',
            'item_product_variant_code' => '[data-test-cart-items] [data-test-cart-item-product] [data-test-product-variant-code="%code%"]',
            'item_quantity' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"] [data-test-cart-item-quantity]',
            'item_unit_price' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"] [data-test-cart-item-unit-price]',
            'item_unit_regular_price' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"] [data-test-cart-item-unit-regular-price]',
            'items_total' => '[data-test-cart-items-total]',
            'no_taxes' => '[data-test-cart-no-tax]',
            'product_row' => '[data-test-cart-item-product-row="%name%"]',
            'product_total' => '[data-test-cart-item-product-row="%name%"] [data-test-cart-product-subtotal]',
            'promotion_coupon' => '[data-test-cart-promotion-coupon]',
            'promotion_total' => '[data-test-cart-promotion-total]',
            'remove_coupon' => '[data-test-cart-promotion-remove-coupon]',
            'remove_item' => '[data-test-cart-items] [data-test-cart-item-product-row="%name%"] [data-test-remove-cart-item]',
            'shipping_total' => '[data-test-cart-shipping-total]',
            'tax_excluded' => '[data-test-cart-tax-excluded]',
            'tax_included' => '[data-test-cart-tax-included]',
            'validation_errors' => '[data-test-validation-error]',
        ]);
    }

    protected function waitForComponentsUpdate(): void
    {
        $this->waitForElementUpdate('form');

        try {
            $this->waitForElementUpdate('summary_component');
        } catch (ElementNotFoundException) {
            return;
        }
    }
}
