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

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    /**
     * @return int
     */
    public function getOnHandQuantityFor(ProductVariantInterface $productVariant): int;

    /**
     * @return int
     */
    public function getOnHoldQuantityFor(ProductVariantInterface $productVariant): int;

    /**
     * @param string $name
     * @param int $position
     */
    public function setPosition(string $name, int $position): void;

    public function savePositions(): void;
}
