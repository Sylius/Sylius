<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\View;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\View\GridView;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin GridView
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GridViewSpec extends ObjectBehavior
{
    function let(Grid $gridDefinition, Parameters $parameters)
    {
        $this->beConstructedWith(['foo', 'bar'], $gridDefinition, $parameters);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\View\GridView');
    }

    function it_has_data()
    {
        $this->getData()->shouldReturn(['foo', 'bar']);
    }

    function it_has_definition(Grid $gridDefinition)
    {
        $this->getDefinition()->shouldReturn($gridDefinition);
    }

    function it_has_parameters(Parameters $parameters)
    {
        $this->getParameters()->shouldReturn($parameters);
    }

    function it_knows_by_which_fields_it_has_been_sorted(Grid $gridDefinition, Parameters $parameters)
    {
        $gridDefinition->hasField('foo')->willReturn(true);
        $gridDefinition->hasField('name')->willReturn(true);
        $gridDefinition->hasField('code')->willReturn(true);

        $gridDefinition->getSorting()->willReturn(['name' => 'desc']);
        $parameters->has('sorting')->willReturn(true);
        $parameters->get('sorting')->willReturn(['code' => 'asc']);

        $this->isSortedBy('foo')->shouldReturn(false);
        $this->isSortedBy('name')->shouldReturn(false);
        $this->isSortedBy('code')->shouldReturn(true);

        $this->getSortingOrder('code')->shouldReturn('asc');
    }

    function it_uses_default_sorting_if_not_provided_in_parameters(Grid $gridDefinition, Parameters $parameters)
    {
        $gridDefinition->hasField('foo')->willReturn(true);
        $gridDefinition->hasField('name')->willReturn(true);
        $gridDefinition->hasField('code')->willReturn(true);

        $gridDefinition->getSorting()->willReturn(['name' => 'desc']);
        $parameters->has('sorting')->willReturn(false);

        $this->isSortedBy('foo')->shouldReturn(false);
        $this->isSortedBy('name')->shouldReturn(true);
        $this->isSortedBy('code')->shouldReturn(false);

        $this->getSortingOrder('name')->shouldReturn('desc');
    }
}
