<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Cart;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class SummaryPage extends SymfonyPage implements SummaryPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_cart_summary';
    }

    /**
     * {@inheritdoc}
     */
    public function getGrandTotal()
    {
        $totalElement = $this->getElement('grand_total');

        return $totalElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxTotal()
    {
        $taxTotalElement = $this->getElement('tax_total');

        return $taxTotalElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTotal()
    {
        $shippingTotalElement = $this->getElement('shipping_total');

        return $shippingTotalElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionTotal()
    {
        $shippingTotalElement = $this->getElement('promotion_total');

        return $shippingTotalElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getItemTotal($productName)
    {
        $itemTotalElement = $this->getElement('product_total', ['%name%' => $productName]);

        return $this->getPriceFromString(trim($itemTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountedTotal($productName)
    {
        $discountedItemTotalElement = $this->getElement('product_discounted_total', ['%name%' => $productName]);

        return $this->getPriceFromString(trim($discountedItemTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function isItemDiscounted($productName)
    {
        return $this->hasElement('product_discounted_total', ['%name%' => $productName]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProduct($productName)
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);
        $itemElement->find('css', 'a.sylius-cart-remove-button')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function changeQuantity($productName, $quantity)
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);
        $itemElement->find('css', 'input[type=number]')->setValue($quantity);

        $this->getElement('save_button')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleItemOnPage()
    {
        $items = $this->getElement('cart_items')->findAll('css', 'tbody > tr');

        return 1 === count($items);
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemNamed($name)
    {
       return $this->hasItemWith($name, '.sylius-product-name');
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemWithVariantNamed($variantName)
    {
       return $this->hasItemWith($variantName, '.sylius-product-variant-name');
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemWithOptionValue($productName, $optionName, $optionValue)
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);

        $selector = sprintf('.sylius-product-options > .item[data-sylius-option-name="%s"]', $optionName);
        $optionValueElement = $itemElement->find('css', $selector);

        if (null === $optionValueElement) {
            throw new ElementNotFoundException($this->getSession(), sprintf('Option value of "%s"', $optionName), 'css', $selector);
        }

        return $optionValue === $optionValueElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return false !== strpos($this->getDocument()->find('css', '.message')->getText(), 'Your cart is empty');
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity($productName)
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);

        return (int) $itemElement->find('css', 'input[type=number]')->getValue();
    }

    public function clearCart()
    {
        $this->getElement('clear_button')->click();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'grand_total' => '#sylius-cart-grand-total',
            'promotion_total' => '#sylius-cart-promotion-total',
            'shipping_total' => '#sylius-cart-shipping-total',
            'tax_total' => '#sylius-cart-tax-total',
            'product_row' => '#sylius-cart-items tbody tr:contains("%name%")',
            'product_total' => '#sylius-cart-items tr:contains("%name%") .sylius-total',
            'product_discounted_total' => '#sylius-cart-items tr:contains("%name%") .sylius-discounted-total',
            'cart_items' => '#sylius-cart-items',
            'clear_button' => '#sylius-cart-clear',
            'save_button' => '#sylius-save',
        ]);
    }

    /**
     * @param $attributeName
     * @param $selector
     *
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    private function hasItemWith($attributeName, $selector)
    {
        $itemsAttributes = $this->getElement('cart_items')->findAll('css', $selector);

        foreach ($itemsAttributes as $itemAttribute) {
            if ($attributeName === $itemAttribute->getText()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $price
     *
     * @return int
     */
    private function getPriceFromString($price)
    {
        return (int) round((str_replace(['€', '£', '$'], '', $price) * 100), 2);
    }
}
