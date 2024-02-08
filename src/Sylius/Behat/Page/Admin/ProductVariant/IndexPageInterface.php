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

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function getOnHandQuantityFor(ProductVariantInterface $productVariant): int;

    public function getOnHoldQuantityFor(ProductVariantInterface $productVariant): int;

    public function setPosition(string $name, int $position): void;

    public function savePositions(): void;

    public function countItemsWithNoName(): int;
}
