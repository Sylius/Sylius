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
final class GridViewSpec extends ObjectBehavior
{
    function let(Grid $gridDefinition, Parameters $parameters)
    {
        $this->beConstructedWith(['foo', 'bar'], $gridDefinition, $parameters);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GridView::class);
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

    function it_knows_which_fields_it_can_be_sorted_by(Grid $gridDefinition, Parameters $parameters)
    {
        $gridDefinition->hasField('foo')->willReturn(true);
        $gridDefinition->hasField('name')->willReturn(true);
        $gridDefinition->hasField('code')->willReturn(true);

        $gridDefinition->getSorting()->willReturn(
            [
                'name' => ['path' => 'name', 'direction' => 'desc'],
                'code' => ['path' => 'code', 'direction' => 'asc'],
            ]
        );

        $parameters->has('sorting')->willReturn(false);

        $this->isSortableBy('name')->shouldReturn(true);
        $this->isSortableBy('code')->shouldReturn(true);
    }

    function it_uses_the_first_sorting_parameter_from_definition_if_not_provided_in_parameters(
        Grid $gridDefinition,
        Parameters $parameters
    ) {
        $gridDefinition->hasField('foo')->willReturn(true);
        $gridDefinition->hasField('name')->willReturn(true);
        $gridDefinition->hasField('code')->willReturn(true);

        $gridDefinition->getSorting()->willReturn(
            [
                'name' => ['path' => 'name', 'direction' => 'desc'],
                'code' => ['path' => 'code', 'direction' => 'asc'],
            ]
        );

        $parameters->has('sorting')->willReturn(false);

        $this->isSortableBy('name')->shouldReturn(true);
        $this->isSortableBy('code')->shouldReturn(true);

        $this->isSortedBy('code')->shouldReturn(false);
        $this->isSortedBy('name')->shouldReturn(true);
    }

    function it_knows_which_field_it_has_been_sorted_by(Grid $gridDefinition, Parameters $parameters)
    {
        $gridDefinition->hasField('foo')->willReturn(true);
        $gridDefinition->hasField('name')->willReturn(true);
        $gridDefinition->hasField('code')->willReturn(true);

        $gridDefinition->getSorting()->willReturn(
            [
                'name' => ['path' => 'name', 'direction' => 'desc'],
                'code' => ['path' => 'code', 'direction' => 'asc'],
            ]
        );

        $parameters->has('sorting')->willReturn(true);
        $parameters->get('sorting')->willReturn(['code' => ['path' => 'code', 'direction' => 'asc']]);

        $this->isSortedBy('name')->shouldReturn(false);
        $this->isSortedBy('code')->shouldReturn(true);
    }

    function it_throws_exception_when_trying_to_sort_by_a_non_existent_field(Grid $gridDefinition)
    {
        $gridDefinition->hasField('code')->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('Field "code" does not exist.'))
            ->during('isSortableBy', ['code'])
        ;
        $this
            ->shouldThrow(new \InvalidArgumentException('Field "code" does not exist.'))
            ->during('isSortedBy', ['code'])
        ;
        $this
            ->shouldThrow(new \InvalidArgumentException('Field "code" does not exist.'))
            ->during('getSortingOrder', ['code'])
        ;
    }

    function it_throws_exception_when_trying_to_sort_by_a_non_sortable_field(Grid $gridDefinition)
    {
        $gridDefinition->hasField('code')->willReturn(true);
        $gridDefinition->hasField('name')->willReturn(true);

        $gridDefinition->getSorting()->willReturn(['code' => ['path' => 'code', 'direction' => 'asc']]);

        $this
            ->shouldThrow(new \InvalidArgumentException('Field "name" is not sortable.'))
            ->during('isSortedBy', ['name'])
        ;
        $this
            ->shouldThrow(new \InvalidArgumentException('Field "name" is not sortable.'))
            ->during('getSortingOrder', ['name'])
        ;
    }
}
