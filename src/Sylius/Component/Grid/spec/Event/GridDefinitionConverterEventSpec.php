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

namespace spec\Sylius\Component\Grid\Event;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class GridDefinitionConverterEventSpec extends ObjectBehavior
{
    function let(Grid $grid)
    {
        $this->beConstructedWith($grid);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GridDefinitionConverterEvent::class);
    }

    function it_has_a_grid(Grid $grid)
    {
        $this->getGrid()->shouldReturn($grid);
    }
}
