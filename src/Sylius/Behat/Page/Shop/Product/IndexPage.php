<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Shop\Product;

use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_product_index';
    }

    public function countProductsItems(): int
    {
        $productsList = $this->getElement('products');

        $products = $productsList->findAll('css', '[data-test-product]');

        return count($products);
    }

    public function getFirstProductNameFromList(): string
    {
        $productsList = $this->getElement('products');

        return $productsList->find('css', '[data-test-product]:first-child [data-test-product-content] [data-test-product-name]')->getText();
    }

    public function getLastProductNameFromList(): string
    {
        $productsList = $this->getElement('products');

        return $productsList->find('css', '[data-test-product]:last-child [data-test-product-content] [data-test-product-name]')->getText();
    }

    public function search(string $name): void
    {
        $this->getDocument()->fillField('criteria_search_value', $name);
        $this->getElement('search_button')->submit();
    }

    public function sort(string $orderNumber): void
    {
        $this->getDocument()->clickLink($orderNumber);
    }

    public function clearFilter(): void
    {
        $this->getElement('clear')->click();
    }

    public function isProductOnList(string $productName): bool
    {
        try {
            $this->getElement('product_name', ['%productName%' => $productName]);
        } catch (ElementNotFoundException) {
            return false;
        }

        return true;
    }

    public function isEmpty(): bool
    {
        return str_contains($this->getElement('validation_message')->getText(), 'There are no results to display');
    }

    public function getProductPrice(string $productName): string
    {
        $element = $this->getElement('product_name', ['%productName%' => $productName]);

        return $element->getParent()->find('css', '[data-test-product-price]')->getText();
    }

    public function getProductOriginalPrice(string $productName): ?string
    {
        $element = $this->getElement('product_name', ['%productName%' => $productName]);
        $originalPriceElement = $element->getParent()->find('css', '[data-test-product-original-price]');

        return ($originalPriceElement !== null) ? $originalPriceElement->getText() : null;
    }

    public function getProductPromotionLabel(string $productName): ?string
    {
        $element = $this->getElement('product_name', ['%productName%' => $productName]);
        $promotionLabelElement = $element->getParent()->find('css', '.sylius_catalog_promotion');

        return ($promotionLabelElement !== null) ? $promotionLabelElement->getText() : null;
    }

    public function isProductOnPageWithName(string $productName): bool
    {
        return $this->hasElement('product_name', ['%productName%' => $productName]);
    }

    public function hasProductsInOrder(array $productNames): bool
    {
        $productsList = $this->getElement('products');
        $products = $productsList->findAll('css', '[data-test-product-content] > [data-test-product-name]');

        foreach ($productNames as $key => $value) {
            if ($products[$key]->getText() !== $value) {
                return false;
            }
        }

        return true;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'clear' => '[data-test-clear]',
            'product_name' => '[data-test-product-name="%productName%"]',
            'products' => '[data-test-products]',
            'search_button' => '[data-test-search]',
            'validation_message' => '[data-test-flash-message]',
        ]);
    }
}
