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

namespace Sylius\Behat\Page\Shop\Cart;

use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Component\Core\Model\ProductInterface;

class SummaryPage extends SymfonyPage implements SummaryPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_cart_summary';
    }

    public function getGrandTotal(): string
    {
        $totalElement = $this->getElement('grand_total');

        return $totalElement->getText();
    }

    public function getBaseGrandTotal(): string
    {
        $totalElement = $this->getElement('base_grand_total');

        return $totalElement->getText();
    }

    public function getIncludedTaxTotal(): string
    {
        $includedTaxTotalElement = $this->getElement('tax_included');

        return $includedTaxTotalElement->getText();
    }

    public function getExcludedTaxTotal(): string
    {
        $excludedTaxTotalElement = $this->getElement('tax_excluded');

        return $excludedTaxTotalElement->getText();
    }

    public function areTaxesCharged(): bool
    {
        try {
            $this->getElement('no_taxes');
        } catch (ElementNotFoundException $exception) {
            return true;
        }

        return false;
    }

    public function getShippingTotal(): string
    {
        $shippingTotalElement = $this->getElement('shipping_total');

        return $shippingTotalElement->getText();
    }

    public function hasShippingTotal(): bool
    {
        return $this->hasElement('shipping_total');
    }

    public function getPromotionTotal(): string
    {
        $shippingTotalElement = $this->getElement('promotion_total');

        return $shippingTotalElement->getText();
    }

    public function getItemsTotal(): string
    {
        $itemsTotalElement = $this->getElement('items_total');

        return $itemsTotalElement->getText();
    }

    public function getItemTotal(string $productName): string
    {
        $itemTotalElement = $this->getElement('product_total', ['%name%' => $productName]);

        return $itemTotalElement->getText();
    }

    public function getItemUnitRegularPrice(string $productName): int
    {
        $regularUnitPrice = $this->getElement('product_unit_regular_price', ['%name%' => $productName]);

        return $this->getPriceFromString(trim($regularUnitPrice->getText()));
    }

    public function getItemUnitPrice(string $productName): int
    {
        $unitPrice = $this->getElement('product_unit_price', ['%name%' => $productName]);

        return $this->getPriceFromString(trim($unitPrice->getText()));
    }

    public function getItemImage(int $itemNumber): string
    {
        $itemImage = $this->getElement('item_image', ['%number%' => $itemNumber]);

        return $itemImage->getAttribute('src');
    }

    public function isItemDiscounted(string $productName): bool
    {
        return $this->hasElement('product_unit_regular_price', ['%name%' => $productName]);
    }

    public function removeProduct(string $productName): void
    {
        $this->getElement('delete_button', ['%name%' => $productName])->press();
    }

    public function applyCoupon(string $couponCode): void
    {
        $this->getElement('coupon_field')->setValue($couponCode);
        $this->getElement('apply_coupon_button')->press();
    }

    public function changeQuantity(string $productName, string $quantity): void
    {
        $this->getElement('item_quantity_input', ['%name%' => $productName])->setValue($quantity);
        $this->getElement('save_button')->click();
    }

    public function isSingleItemOnPage(): bool
    {
        $items = $this->getElement('cart_items')->findAll('css', '[data-test-cart-product-row]');

        return 1 === count($items);
    }

    public function hasItemNamed(string $name): bool
    {
        return $this->hasItemWith($name, '[data-test-product-name]');
    }

    public function hasItemWithVariantNamed(string $variantName): bool
    {
        return $this->hasItemWith($variantName, '[data-test-product-variant-name]');
    }

    public function hasItemWithOptionValue(string $productName, string $optionName, string $optionValue): bool
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);

        $selector = sprintf('[data-test-product-options] [data-test-option-name="%s"]', $optionName);
        $optionValueElement = $itemElement->find('css', $selector);

        if (null === $optionValueElement) {
            throw new ElementNotFoundException($this->getSession(), sprintf('ProductOption value of "%s"', $optionName), 'css', $selector);
        }

        return $optionValue === $optionValueElement->getText();
    }

    public function hasItemWithCode(string $code): bool
    {
        return $this->hasItemWith($code, '[data-test-product-variant-code]');
    }

    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool
    {
        $message = sprintf('%s does not have sufficient stock.', $product->getName());

        try {
            return $this->getElement('validation_errors')->getText() === $message;
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }

    public function isEmpty(): bool
    {
        return false !== strpos($this->getElement('flash_message')->getText(), 'Your cart is empty');
    }

    public function getQuantity(string $productName): int
    {
        return (int) $this->getElement('item_quantity_input', ['%name%' => $productName])->getValue();
    }

    public function getCartTotal(): string
    {
        $cartTotalText = $this->getElement('cart_total')->getText();

        if (strpos($cartTotalText, ',') !== false) {
            return strstr($cartTotalText, ',', true);
        }

        return trim($cartTotalText);
    }

    public function clearCart(): void
    {
        $this->getElement('clear_button')->click();
    }

    public function updateCart(): void
    {
        $this->getElement('update_button')->click();
    }

    public function waitForRedirect(int $timeout): void
    {
        $this->getDocument()->waitFor($timeout, function () {
            return $this->isOpen();
        });
    }

    public function getPromotionCouponValidationMessage(): string
    {
        return $this->getElement('promotion_coupon_validation_message')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'apply_coupon_button' => '[data-test-apply-coupon-button]',
            'base_grand_total' => '[data-test-cart-base-grand-total]',
            'cart_items' => '[data-test-cart-items]',
            'cart_total' => '[data-test-cart-total]',
            'clear_button' => '[data-test-cart-clear-button]',
            'coupon_field' => '[data-test-cart-promotion-coupon-input]',
            'delete_button' => '[data-test-cart-remove-button="%name%"]',
            'flash_message' => '[data-test-flash-message]',
            'grand_total' => '[data-test-cart-grand-total]',
            'item_image' => '[data-test-cart-item="%number%"] [data-test-main-image]',
            'item_quantity_input' => '[data-test-cart-item-quantity-input="%name%"]',
            'items_total' => '[data-test-cart-items-total]',
            'no_taxes' => '[data-test-cart-no-tax]',
            'product_row' => '[data-test-cart-product-row="%name%"]',
            'product_total' => '[data-test-cart-product-row="%name%"] [data-test-cart-product-subtotal]',
            'product_unit_price' => '[data-test-cart-product-row="%name%"] [data-test-cart-product-unit-price]',
            'product_unit_regular_price' => '[data-test-cart-product-row="%name%"] [data-test-cart-product-regular-unit-price]',
            'promotion_coupon_validation_message' => '[data-test-cart-promotion-coupon] [data-test-validation-error]',
            'promotion_total' => '[data-test-cart-promotion-total]',
            'save_button' => '[data-test-apply-coupon-button]',
            'shipping_total' => '[data-test-cart-shipping-total]',
            'tax_excluded' => '[data-test-cart-tax-exluded]',
            'tax_included' => '[data-test-cart-tax-included]',
            'update_button' => '[data-test-cart-update-button]',
            'validation_errors' => '[data-test-validation-error]',
        ]);
    }

    /**
     * @param string|array $selector
     *
     * @throws ElementNotFoundException
     */
    private function hasItemWith(string $attributeName, $selector): bool
    {
        $itemsAttributes = $this->getElement('cart_items')->findAll('css', $selector);

        foreach ($itemsAttributes as $itemAttribute) {
            if ($attributeName === $itemAttribute->getText()) {
                return true;
            }
        }

        return false;
    }

    private function getPriceFromString(string $price): int
    {
        return (int) round((float) str_replace(['€', '£', '$'], '', $price) * 100, 2);
    }
}
