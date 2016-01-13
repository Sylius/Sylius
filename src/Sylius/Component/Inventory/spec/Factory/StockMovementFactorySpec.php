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
use Sylius\Component\Inventory\Factory\StockMovementFactory;
use Sylius\Component\Inventory\Factory\StockMovementFactoryInterface;
use Sylius\Component\Inventory\Model\StockItem;
use Sylius\Component\Inventory\Repository\StockMovementRepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockMovementFactorySpec extends ObjectBehavior
{
    function let(StockMovementRepositoryInterface $stockMovementRepository)
    {
        $this->beConstructedWith(StockItem::class, $stockMovementRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(StockMovementFactory::class);
    }

    function it_is_a_factory()
    {
        $this->shouldHaveType(Factory::class);
    }

    function it_implements_inventory_unit_factory_interface()
    {
        $this->shouldImplement(StockMovementFactoryInterface::class);
    }
}
