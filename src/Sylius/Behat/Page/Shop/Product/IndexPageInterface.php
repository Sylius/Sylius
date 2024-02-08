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

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface IndexPageInterface extends PageInterface
{
    public function countProductsItems(): int;

    public function getFirstProductNameFromList(): string;

    public function getLastProductNameFromList(): string;

    public function search(string $name): void;

    public function sort(string $orderNumber): void;

    public function clearFilter(): void;

    public function isProductOnList(string $productName): bool;

    public function isEmpty(): bool;

    public function getProductPrice(string $productName): string;

    public function getProductOriginalPrice(string $productName): ?string;

    public function getProductPromotionLabel(string $productName): ?string;

    public function isProductOnPageWithName(string $name): bool;

    public function hasProductsInOrder(array $productNames): bool;
}
