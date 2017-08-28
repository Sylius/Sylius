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

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class GridDefinitionConverterEventSpec extends ObjectBehavior
{
    function let(Grid $grid): void
    {
        $this->beConstructedWith($grid);
    }

    function it_has_a_grid(Grid $grid): void
    {
        $this->getGrid()->shouldReturn($grid);
    }
}
