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

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as CrudIndexPageInterface;

interface IndexPageInterface extends CrudIndexPageInterface
{
    public function filterByTaxon(string $taxonName): void;

    public function hasProductAccessibleImage(string $productCode): bool;

    public function showProductPage(string $productName): void;

    public function chooseChannelFilter(string $channelName): void;

    public function filter(): void;

    public function goToPage(int $page): void;

    public function checkFirstProductHasDataAttribute(string $attributeName): bool;

    public function checkLastProductHasDataAttribute(string $attributeName): bool;

    public function getPageNumber(): int;
}
