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

interface IndexPageInterface
{
    /**
     * @return int
     */
    public function countProductsItems(): int;

    /**
     * @return string
     */
    public function getFirstProductNameFromList(): string;

    /**
     * @return string
     */
    public function getLastProductNameFromList(): string;

    /**
     * @param string $name
     */
    public function search(string $name): void;

    /**
     * @param string $order
     */
    public function sort(string $order): void;

    public function clearFilter(): void;

    /**
     * @param string $productName
     *
     * @return bool
     */
    public function isProductOnList(string $productName): bool;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @param string $productName
     *
     * @return string
     */
    public function getProductPrice(string $productName): string;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isProductOnPageWithName(string $name): bool;

    /**
     * @param array $productNames
     *
     * @return bool
     */
    public function hasProductsInOrder(array $productNames): bool;
}
