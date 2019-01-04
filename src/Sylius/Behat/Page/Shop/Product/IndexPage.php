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

namespace Sylius\Behat\Page\Shop\Product;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'sylius_shop_product_index';
    }

    /**
     * {@inheritdoc}
     */
    public function countProductsItems()
    {
        $productsList = $this->getDocument()->find('css', '#products');

        $products = $productsList->findAll('css', '.card');

        return count($products);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstProductNameFromList()
    {
        $productsList = $this->getDocument()->find('css', '#products');

        return $productsList->find('css', '.card:first-child .content > a')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastProductNameFromList()
    {
        $productsList = $this->getDocument()->find('css', '#products');

        return $productsList->find('css', '.card:last-child .content > a')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function search($name)
    {
        $this->getDocument()->fillField('criteria_search_value', $name);
        $this->getDocument()->pressButton('Search');
    }

    /**
     * {@inheritdoc}
     */
    public function sort($order)
    {
        $this->getDocument()->clickLink($order);
    }

    public function clearFilter()
    {
        $this->getDocument()->clickLink('Clear');
    }

    /**
     * {@inheritdoc}
     */
    public function isProductOnList($productName)
    {
        return null !== $this->getDocument()->find('css', sprintf('.sylius-product-name:contains("%s")', $productName));
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return false !== strpos($this->getDocument()->find('css', '.message')->getText(), 'There are no results to display');
    }

    /**
     * {@inheritdoc}
     */
    public function getProductPrice($productName)
    {
        $container = $this->getDocument()->find('css', sprintf('.sylius-product-name:contains("%s")', $productName))->getParent();

        return $container->find('css', '.sylius-product-price')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function isProductOnPageWithName($name)
    {
        return null !== $this->getDocument()->find('css', sprintf('.content > a:contains("%s")', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductsInOrder(array $productNames)
    {
        $productsList = $this->getDocument()->find('css', '#products');
        $products = $productsList->findAll('css', '.card  .content > .sylius-product-name');

        foreach ($productNames as $key => $value) {
            if ($products[$key]->getText() !== $value) {
                return false;
            }
        }

        return true;
    }
}
