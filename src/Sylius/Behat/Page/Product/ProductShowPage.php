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

use Sylius\Behat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Component\Product\Model\ProductInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ProductShowPage extends SymfonyPage
{
    /**
     * @param ProductInterface $product
     *
     * @return ProductShowPage
     */
    public function openSpecificProductPage(ProductInterface $product)
    {
        $url = $this->router->generate($product);
        $this->getSession()->visit($url);

        return $this;
    }

    public function addToCart()
    {
        $this->pressButton('Add to cart');
    }

    /**
     * @param string $quantity
     */
    public function addToCartWithQuantity($quantity)
    {
        $this->fillField('Quantity', $quantity);
        $this->pressButton('Add to cart');
    }

    /**
     */
    public function getRouteName()
    {
        return null;
    }
}
