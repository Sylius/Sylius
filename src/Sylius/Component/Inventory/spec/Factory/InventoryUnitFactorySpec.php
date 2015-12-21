<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Factory\InventoryUnitFactoryInterface;
use Sylius\Component\Inventory\Model\InventoryUnit;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryUnitFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(InventoryUnit::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Factory\InventoryUnitFactory');
    }

    function it_is_a_factory()
    {
        $this->shouldHaveType(Factory::class);
    }

    function it_implements_inventory_unit_factory_interface()
    {
        $this->shouldImplement(InventoryUnitFactoryInterface::class);
    }

    function it_throws_exception_if_given_quantity_is_less_than_1(StockableInterface $stockable)
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('createForStockable', array($stockable, -2))
        ;
    }

    function it_creates_specified_amount_of_inventory_units(StockableInterface $stockable)
    {
        $this->createForStockable($stockable, 3, InventoryUnitInterface::STATE_BACKORDERED)->shouldHaveCount(3);
    }
}
