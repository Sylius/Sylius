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

use Sylius\Behat\Page\PageInterface;

interface IndexPageInterface extends PageInterface
{
    public function countProductsItems(): int;

    public function getFirstProductNameFromList(): string;

    public function getLastProductNameFromList(): string;

    public function search(string $name);

    public function sort(string $order);

    public function clearFilter();

    public function isProductOnList(string $productName): bool;

    public function isEmpty(): bool;

    public function getProductPrice(string $productName): string;

    public function isProductOnPageWithName(string $name): bool;

    public function hasProductsInOrder(array $productNames): bool;
}
