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

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CartSummaryPage extends SymfonyPage implements CartSummaryPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getGrandTotal()
    {
        $grandTotalElement = $this->getElement('grand total');

        return trim(str_replace('Grand total:', '', $grandTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxTotal()
    {
        $taxTotalElement = $this->getElement('tax total');

        return trim(str_replace('Tax total:', '', $taxTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTotal()
    {
        $shippingTotalElement = $this->getElement('shipping total');

        return trim(str_replace('Shipping total:', '', $shippingTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionTotal()
    {
        $shippingTotalElement = $this->getElement('promotion total');

        return trim(str_replace('Promotion total:', '', $shippingTotalElement->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getItemRegularPrice($productName)
    {
        $regularPriceElement = $this->getElement('product regular price', ['%name%' => $productName]);

        return trim($regularPriceElement->getText());
    }

    /**
     * {@inheritdoc}
     */
    public function getItemDiscountPrice($productName)
    {
        $discountPriceElement = $this->getElement('product discount price', ['%name%' => $productName]);

        return trim($discountPriceElement->getText());
    }

    /**
     * {@inheritdoc}
     */
    public function isItemDiscounted($productName)
    {
        return $this->hasElement('product discount price', ['%name%' => $productName]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProduct($productName)
    {
        $itemElement = $this->getElement('product row', ['%name%' => $productName]);
        $itemElement->find('css', 'a#remove-button')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function changeQuantity($productName, $quantity)
    {
        $itemElement = $this->getElement('product row', ['%name%' => $productName]);
        $itemElement->find('css', 'input[type=number]')->setValue($quantity);

        $this->getDocument()->pressButton('Save');
    }

    /**
     * {@inheritdoc}
     */
    public function isElementOnPage($elementName)
    {
        $items = $this->getElement('cart items')->findAll('css', 'thead > tr > th');
        $summary = $this->getElement('cart summary')->findAll('css', 'tbody > tr > td > strong');
        $elements = array_merge($items, $summary);

        foreach($elements as $row) {
            if($elementName === $row->getText()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        $totalElement = $this->getElement('total');

        return $totalElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitPrice()
    {
        $unitPriceElement = $this->getElement('unit price');

        return $unitPriceElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        $quantityElement = $this->getElement('quantity', [ '%number%' => 0]);

        return $quantityElement->getValue();
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
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'grand total' => '#cart-summary td:contains("Grand total")',
            'promotion total' => '#cart-summary td:contains("Promotion total")',
            'shipping total' => '#cart-summary td:contains("Shipping total")',
            'tax total' => '#cart-summary td:contains("Tax total")',
            'product row' => '#cart-items tbody tr:contains("%name%")',
            'product regular price' => '#cart-summary tr:contains("%name%") .regular-price',
            'product discount price' => '#cart-summary tr:contains("%name%") .discount-price',
            'total' => '.total',
            'quantity' => '#sylius_cart_items_%number%_quantity',
            'unit price' => '.unit-price',
            'cart items' => '#cart-items',
            'cart summary' => '#cart-summary',
        ]);
    }
}
