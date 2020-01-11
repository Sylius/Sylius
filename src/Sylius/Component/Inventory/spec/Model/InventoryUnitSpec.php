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

namespace spec\Sylius\Component\Inventory\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

final class InventoryUnitSpec extends ObjectBehavior
{
    function it_implements_inventory_unit_interface(): void
    {
        $this->shouldImplement(InventoryUnitInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_defined_stockable_subject_by_default(): void
    {
        $this->getStockable()->shouldReturn(null);
    }

    function it_allows_defining_stockable_subject(StockableInterface $stockable): void
    {
        $this->setStockable($stockable);
        $this->getStockable()->shouldReturn($stockable);
    }
}
