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

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as CrudIndexPageInterface;

interface IndexPerTaxonPageInterface extends CrudIndexPageInterface
{
    public function hasProductsInOrder(array $productNames): bool;

    public function setPositionOfProduct(string $productName, int $position): void;

    public function savePositions(): void;
}
