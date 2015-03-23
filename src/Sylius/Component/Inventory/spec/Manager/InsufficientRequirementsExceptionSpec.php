<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Manager;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Model\StockInterface;

/**
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class InsufficientRequirementsExceptionSpec extends ObjectBehavior
{
    function let(StockableInterface $stockable, StockInterface $stock)
    {
        $stockable->getInventoryName()->shouldBeCalled()->willReturn('Product Name');

        $stockable->getStock()->willReturn($stock);
        $this->beConstructedWith($stockable, 10);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Manager\InsufficientRequirementsException');
    }

    function it_is_an_uderflow_exception()
    {
        $this->shouldHaveType('\UnderflowException');
    }

    function it_returns_its_stockable()
    {
        $this->getStockable()->shouldHaveType('Sylius\Component\Inventory\Model\StockableInterface');
    }
}
