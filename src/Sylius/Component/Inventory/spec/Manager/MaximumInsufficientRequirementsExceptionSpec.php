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
class MaximumInsufficientRequirementsExceptionSpec extends ObjectBehavior
{
    function let(StockableInterface $stockable, StockInterface $stock)
    {
        $stockable->getInventoryName()->shouldBeCalled()->willReturn('Product Name');

        $stockable->getStock()->willReturn($stock);
        $this->beConstructedWith($stockable, 1, 2);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Manager\MaximumInsufficientRequirementsException');
    }

    function it_is_an_insufficientrequirements_exception()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Manager\InsufficientRequirementsException');
    }
}
