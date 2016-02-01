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

use Sylius\Behat\PageObjectExtension\Page\SymfonyPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CartSummaryPage extends SymfonyPage
{
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
