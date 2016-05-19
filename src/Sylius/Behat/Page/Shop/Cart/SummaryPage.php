<?php

/*
 * This file is part of the Sylius package.
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
    public function getGrandTotal()
    {
        $grandTotalElement = $this->getElement('grand_total');

        return trim(str_replace('Grand total:', '', $grandTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxTotal()
    {
        $taxTotalElement = $this->getElement('tax_total');

        return trim(str_replace('Tax total:', '', $taxTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTotal()
    {
        $shippingTotalElement = $this->getElement('shipping_total');

        return trim(str_replace('Shipping total:', '', $shippingTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionTotal()
    {
        $shippingTotalElement = $this->getElement('promotion_total');

        return trim(str_replace('Promotion total:', '', $shippingTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getItemRegularPrice($productName)
    {
        $regularPriceElement = $this->getElement('product_regular_price', ['%name%' => $productName]);

        return $this->getPriceFromString(trim($regularPriceElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountPrice($productName)
    {
        $discountPriceElement = $this->getElement('product_discount_price', ['%name%' => $productName]);

        return $this->getPriceFromString(trim($discountPriceElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function isItemDiscounted($productName)
    {
        return $this->hasElement('product_discount_price', ['%name%' => $productName]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProduct($productName)
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);
        $itemElement->find('css', 'a#remove-button')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function changeQuantity($productName, $quantity)
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);
        $itemElement->find('css', 'input[type=number]')->setValue($quantity);

        $this->getDocument()->pressButton('Save');
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
    public function isItemWithName($name)
    {
       return $this->findItemWith($name, 'tbody  tr > td > div > a > strong');
    }

    /**
     * {@inheritdoc}
     */
    public function isItemWithVariant($variantName)
    {
       return $this->findItemWith($variantName, 'tbody  tr > td > strong');
    }

    /**
     * {@inheritdoc}
     */
    public function getProductOption($productName, $optionName)
    {
        $itemElement = $this->getElement('product_row', ['%name%' => $productName]);

        return $itemElement->find('css', sprintf('li:contains("%s")', ucfirst($optionName)))->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        $isEmpty = strpos($this->getDocument()->find('css', '.message')->getText(), 'Your cart is empty');
        if (false === $isEmpty ) {
            return false;
        }

        return true;
    }

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
            'grand_total' => '#cart-summary td:contains("Grand total")',
            'promotion_total' => '#cart-summary td:contains("Promotion total")',
            'shipping_total' => '#cart-summary td:contains("Shipping total")',
            'tax_total' => '#cart-summary td:contains("Tax total")',
            'product_row' => '#cart-items tbody tr:contains("%name%")',
            'product_regular_price' => '#cart-items tr:contains("%name%") .regular-price',
            'product_discount_price' => '#cart-items tr:contains("%name%") .discount-price',
            'total' => '.total',
            'quantity' => '#sylius_cart_items_%number%_quantity',
            'unit_price' => '.unit-price',
            'cart_items' => '#cart-items',
            'cart_summary' => '#cart-summary',
            'clear_button' => 'a[class*="clear"]',
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
    private function findItemWith($attributeName, $selector)
    {
        $itemsAttributes = $this->getElement('cart_items')->findAll('css', $selector);

        foreach($itemsAttributes as $itemAttribute) {
            if($attributeName === $itemAttribute->getText()) {
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
