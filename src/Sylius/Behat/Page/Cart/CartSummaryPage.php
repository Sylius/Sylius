<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Cart;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CartSummaryPage extends SymfonyPage implements CartSummaryPageInterface
{
    /**
     * @var array
     */
    protected $elements = [
        'grand total' => '#cart-summary td:contains("Grand total")',
        'promotion total' => '#cart-summary td:contains("Promotion total")',
        'shipping total' => '#cart-summary td:contains("Shipping total")',
        'tax total' => '#cart-summary td:contains("Tax total")',
    ];

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
    public function removeProduct($productName)
    {
        $item = $this->getDocument()->find('css', sprintf('#cart-summary tbody tr:contains("%s")', $productName));
        $item->find('css', 'a.btn-danger')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function changeQuantity($productName, $quantity)
    {
        $item = $this->getDocument()->find('css', sprintf('#cart-summary tbody tr:contains("%s")', $productName));
        $field = $item->find('css', 'input[type=number]');
        $field->setValue($quantity);

        $this->getDocument()->pressButton('Save');
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteName()
    {
        return 'sylius_cart_summary';
    }
}
