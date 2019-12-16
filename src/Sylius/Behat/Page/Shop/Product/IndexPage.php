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
    public function getRouteName(): string
    {
        return 'sylius_shop_product_index';
    }

    public function countProductsItems(): int
    {
        $productsList = $this->getDocument()->find('css', '#products');

        $products = $productsList->findAll('css', '.card');

        return count($products);
    }

    public function getFirstProductNameFromList(): string
    {
        $productsList = $this->getDocument()->find('css', '#products');

        return $productsList->find('css', '.card:first-child .content > a')->getText();
    }

    public function getLastProductNameFromList(): string
    {
        $productsList = $this->getDocument()->find('css', '#products');

        return $productsList->find('css', '.card:last-child .content > a')->getText();
    }

    public function search(string $name): void
    {
        $this->getDocument()->fillField('criteria_search_value', $name);
        $this->getDocument()->pressButton('Search');
    }

    public function sort(string $orderNumber): void
    {
        $this->getDocument()->clickLink($orderNumber);
    }

    public function clearFilter(): void
    {
        $this->getDocument()->clickLink('Clear');
    }

    public function isProductOnList(string $productName): bool
    {
        return null !== $this->getDocument()->find('css', sprintf('.sylius-product-name:contains("%s")', $productName));
    }

    public function isEmpty(): bool
    {
        return false !== strpos($this->getDocument()->find('css', '.message')->getText(), 'There are no results to display');
    }

    public function getProductPrice(string $productName): string
    {
        $container = $this->getDocument()->find('css', sprintf('.sylius-product-name:contains("%s")', $productName))->getParent();

        return $container->find('css', '.sylius-product-price')->getText();
    }

    public function isProductOnPageWithName(string $name): bool
    {
        return null !== $this->getDocument()->find('css', sprintf('.content > a:contains("%s")', $name));
    }

    public function hasProductsInOrder(array $productNames): bool
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
