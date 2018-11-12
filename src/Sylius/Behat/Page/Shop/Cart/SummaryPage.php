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
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Component\Core\Model\ProductInterface;

class SummaryPage extends SymfonyPage implements SummaryPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'sylius_shop_cart_summary';
    }

    /**
     * {@inheritdoc}
     */
    public function getGrandTotal(): string
    {
        $totalElement = $this->getElement('grand_total');

        return $totalElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseGrandTotal(): string
    {
        $totalElement = $this->getElement('base_grand_total');

        return $totalElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxTotal(): string
    {
        $taxTotalElement = $this->getElement('tax_total');

        return $taxTotalElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTotal(): string
    {
        $shippingTotalElement = $this->getElement('shipping_total');

        return $shippingTotalElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionTotal(): string
    {
        $shippingTotalElement = $this->getElement('promotion_total');

        return $shippingTotalElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getItemTotal(string $productName): string
    {
        $itemTotalElement = $this->getElement('product_total', ['%name%' => $productName]);

        return  $itemTotalElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getItemUnitRegularPrice(string $productName): string
    {
        $regularUnitPrice = $this->getElement('product_unit_regular_price', ['%name%' => $productName]);

        return $this->getPriceFromString(trim($regularUnitPrice->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getItemUnitPrice(string $productName): string
    {
        $unitPrice = $this->getElement('product_unit_price', ['%name%' => $productName]);

        return $this->getPriceFromString(trim($unitPrice->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function isItemDiscounted(string $productName): bool
    {
        return $this->hasElement('product_unit_regular_price', ['%name%' => $productName]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProduct(string $productName): void
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);
        $itemElement->find('css', 'button.sylius-cart-remove-button')->press();
    }

    /**
     * {@inheritdoc}
     */
    public function applyCoupon(string $couponCode): void
    {
        $this->getElement('coupon_field')->setValue($couponCode);
        $this->getElement('apply_coupon_button')->press();
    }

    /**
     * {@inheritdoc}
     */
    public function changeQuantity(string $productName, int $quantity): void
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);
        $itemElement->find('css', 'input[type=number]')->setValue($quantity);

        $this->getElement('save_button')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleItemOnPage(): bool
    {
        $items = $this->getElement('cart_items')->findAll('css', 'tbody > tr');

        return 1 === count($items);
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemNamed(string $name): bool
    {
        return $this->hasItemWith($name, '.sylius-product-name');
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemWithVariantNamed(string $variantName): bool
    {
        return $this->hasItemWith($variantName, '.sylius-product-variant-name');
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemWithOptionValue(string $productName, string $optionName, string $optionValue): string
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);

        $selector = sprintf('.sylius-product-options > .item[data-sylius-option-name="%s"]', $optionName);
        $optionValueElement = $itemElement->find('css', $selector);

        if (null === $optionValueElement) {
            throw new ElementNotFoundException($this->getSession(), sprintf('ProductOption value of "%s"', $optionName), 'css', $selector);
        }

        return $optionValue === $optionValueElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemWithCode(string $code): bool
    {
        return $this->hasItemWith($code, '.sylius-product-variant-code');
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductOutOfStockValidationMessage(ProductInterface $product): bool
    {
        $message = sprintf('%s does not have sufficient stock.', $product->getName());

        try {
            return $this->getElement('validation_errors')->getText() === $message;
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return false !== strpos($this->getDocument()->find('css', '.message')->getText(), 'Your cart is empty');
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity(string $productName): int
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);

        return (int) $itemElement->find('css', 'input[type=number]')->getValue();
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function waitForRedirect(int $timeout): void
    {
        $this->getDocument()->waitFor($timeout, function () {
            return $this->isOpen();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionCouponValidationMessage(): string
    {
        return $this->getElement('promotion_coupon_validation_message')->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'apply_coupon_button' => 'button:contains("Apply coupon")',
            'cart_items' => '#sylius-cart-items',
            'cart_total' => '#sylius-cart-total',
            'clear_button' => '#sylius-cart-clear',
            'coupon_field' => '#sylius_cart_promotionCoupon',
            'grand_total' => '#sylius-cart-grand-total',
            'base_grand_total' => '#sylius-cart-base-grand-total',
            'product_discounted_total' => '#sylius-cart-items tr:contains("%name%") .sylius-discounted-total',
            'product_row' => '#sylius-cart-items tbody tr:contains("%name%")',
            'product_total' => '#sylius-cart-items tr:contains("%name%") .sylius-total',
            'product_unit_price' => '#sylius-cart-items tr:contains("%name%") .sylius-unit-price',
            'product_unit_regular_price' => '#sylius-cart-items tr:contains("%name%") .sylius-regular-unit-price',
            'promotion_coupon_validation_message' => '#sylius-coupon .sylius-validation-error',
            'promotion_total' => '#sylius-cart-promotion-total',
            'save_button' => '#sylius-save',
            'shipping_total' => '#sylius-cart-shipping-total',
            'tax_total' => '#sylius-cart-tax-total',
            'update_button' => '#sylius-cart-update',
            'validation_errors' => '.sylius-validation-error',
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
