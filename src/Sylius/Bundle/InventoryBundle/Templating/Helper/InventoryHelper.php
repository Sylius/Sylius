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

namespace Sylius\Bundle\InventoryBundle\Templating\Helper;

use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Symfony\Component\Templating\Helper\Helper;

final class InventoryHelper extends Helper
{
    public function __construct(private AvailabilityCheckerInterface $checker)
    {
    }

    public function isStockAvailable(StockableInterface $stockable): bool
    {
        return $this->checker->isStockAvailable($stockable);
    }

    public function isStockSufficient(StockableInterface $stockable, int $quantity): bool
    {
        return $this->checker->isStockSufficient($stockable, $quantity);
    }

    public function getName(): string
    {
        return 'sylius_inventory';
    }
}
