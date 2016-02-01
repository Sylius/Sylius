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

use Sylius\Behat\SymfonyPageObjectExtension\Page\SymfonyPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CartSummaryPage extends SymfonyPage
{
    /**
     * @var array
     */
    protected $elements = [
        'grand total' => '#cart-summary td:contains("Grand total")',
        'tax total' => '#cart-summary td:contains("Tax total")',
    ];

    /**
     * @return string
     */
    public function getGrandTotal()
    {
        $grandTotalElement = $this->getElement('grand total');

        return trim(str_replace('Grand total:', '', $grandTotalElement->getText()));
    }

    /**
     * @return string
     */
    public function getTaxTotal()
    {
        $taxTotalElement = $this->getElement('grand total');

        return trim(str_replace('Tax total:', '', $taxTotalElement->getText()));
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_cart_summary';
    }

    /**
     * @param string $productName
     */
    public function removeProduct($productName)
    {
        $item = $this->find('css', sprintf('#cart-summary tbody tr:contains("%s")', $productName));
        $item->find('css', 'a.btn-danger')->click();
    }

    /**
     * @param string $productName
     * @param int $quantity
     */
    public function changeQuantity($productName, $quantity)
    {
        $item = $this->find('css', sprintf('#cart-summary tbody tr:contains("%s")', $productName));
        $field = $item->find('css', 'input[type=number]');
        $field->setValue($quantity);

        $this->pressButton('Save');
    }
}
