<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Product;

use Sylius\Behat\SymfonyPageObjectExtension\PageObject\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ProductShowPage extends SymfonyPage
{
    /**
     * @param array $urlParameters
     *
     * @return ProductShowPage
     */
    public function open(array $urlParameters = [])
    {
        $url = $this->router->generate($urlParameters['product']);
        $this->getSession()->visit($url);

        return $this;
    }

    public function addToCart()
    {
        $this->getDocument()->pressButton('Add to cart');
    }

    /**
     * @param string $quantity
     */
    public function addToCartWithQuantity($quantity)
    {
        $this->getDocument()->fillField('Quantity', $quantity);
        $this->getDocument()->pressButton('Add to cart');
    }

    /**
     * @param string $variant
     */
    public function addToCartWithVariant($variant)
    {
        $item = $this->getDocument()->find('css', sprintf('#product-variants tbody tr:contains("%s")', $variant));
        $radio = $item->find('css', 'input');

        $this->getDocument()->fillField($radio->getAttribute('name'), $radio->getAttribute('value'));

        $this->getDocument()->pressButton('Add to cart');
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
    }
}
