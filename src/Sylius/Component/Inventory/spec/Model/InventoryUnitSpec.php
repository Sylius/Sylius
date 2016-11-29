<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Model\InventoryUnit;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class InventoryUnitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(InventoryUnit::class);
    }

    function it_implements_inventory_unit_interface()
    {
        $this->shouldImplement(InventoryUnitInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_defined_stockable_subject_by_default()
    {
        $this->getStockable()->shouldReturn(null);
    }

    function it_allows_defining_stockable_subject(StockableInterface $stockable)
    {
        $this->setStockable($stockable);
        $this->getStockable()->shouldReturn($stockable);
    }
}
