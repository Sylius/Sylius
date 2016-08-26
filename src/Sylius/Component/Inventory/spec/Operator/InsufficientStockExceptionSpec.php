<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Operator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Operator\InsufficientStockException;

/**
 * @mixin InsufficientStockException
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
final class InsufficientStockExceptionSpec extends ObjectBehavior
{
    function let(StockableInterface $stockable)
    {
        $this->beConstructedWith($stockable, 10);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InsufficientStockException::class);
    }

    function it_is_an_underflow_exception()
    {
        $this->shouldHaveType(\UnderflowException::class);
    }

    function it_returns_its_stockable(StockableInterface $stockable)
    {
        $this->getStockable()->shouldReturn($stockable);
    }
}
