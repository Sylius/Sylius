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
use Sylius\Component\Inventory\Factory\StockItemFactory;
use Sylius\Component\Inventory\Factory\StockItemFactoryInterface;
use Sylius\Component\Inventory\Model\StockItem;
use Sylius\Component\Inventory\Repository\StockItemRepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Inventory\Repository\StockLocationRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockItemFactorySpec extends ObjectBehavior
{
    function let(StockItemRepositoryInterface $stockItemRepository,
                 ObjectManager $stockItemManager,
                 StockLocationRepositoryInterface $stockLocationRepository,
                 RepositoryInterface $stockableRepository)
    {
        $this->beConstructedWith(StockItem::class,
            $stockItemRepository,
            $stockItemManager,
            $stockLocationRepository,
            $stockableRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(StockItemFactory::class);
    }

    function it_is_a_factory()
    {
        $this->shouldHaveType(Factory::class);
    }

    function it_implements_inventory_unit_factory_interface()
    {
        $this->shouldImplement(StockItemFactoryInterface::class);
    }
}
