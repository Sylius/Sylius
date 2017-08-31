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

namespace spec\Sylius\Component\Grid\View;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\View\GridViewInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class GridViewSpec extends ObjectBehavior
{
    function let(Grid $gridDefinition): void
    {
        $this->beConstructedWith(['foo', 'bar'], $gridDefinition, new Parameters());
    }

    function it_implements_a_grid_view_interface(): void
    {
        $this->shouldHaveType(GridViewInterface::class);
    }

    function it_has_data(): void
    {
        $this->getData()->shouldReturn(['foo', 'bar']);
    }

    function it_has_definition(Grid $gridDefinition): void
    {
        $this->getDefinition()->shouldReturn($gridDefinition);
    }

    function it_has_parameters(): void
    {
        $this->getParameters()->shouldBeLike(new Parameters());
    }

    function it_uses_the_default_sorting_from_definition_if_not_provided_in_parameters(
        Grid $gridDefinition,
        Field $codeField,
        Field $nameField
    ): void {
        $gridDefinition->hasField('foo')->willReturn(true);

        $gridDefinition->hasField('code')->willReturn(true);
        $gridDefinition->getField('code')->willReturn($codeField);
        $codeField->isSortable()->willReturn(true);

        $gridDefinition->hasField('name')->willReturn(true);
        $gridDefinition->getField('name')->willReturn($nameField);
        $nameField->isSortable()->willReturn(true);
        $nameField->getSortable()->willReturn('name');

        $gridDefinition->getSorting()->willReturn(['name' => 'asc']);

        $this->isSortedBy('code')->shouldReturn(false);
        $this->isSortedBy('name')->shouldReturn(true);
    }

    function it_knows_which_field_it_has_been_sorted_by(Grid $gridDefinition, Field $codeField, Field $nameField): void
    {
        $this->beConstructedWith(['foo', 'bar'], $gridDefinition, new Parameters([
            'sorting' => ['name' => ['direction' => 'asc']],
        ]));

        $gridDefinition->hasField('foo')->willReturn(true);

        $gridDefinition->hasField('name')->willReturn(true);
        $gridDefinition->getField('name')->willReturn($nameField);
        $nameField->isSortable()->willReturn(true);
        $nameField->getSortable()->willReturn('name');

        $gridDefinition->hasField('code')->willReturn(true);
        $gridDefinition->getField('code')->willReturn($codeField);
        $codeField->isSortable()->willReturn(true);
        $codeField->getSortable()->willReturn('code');

        $gridDefinition->getSorting()->willReturn(['code' => ['order' => 'desc']]);

        $this->isSortedBy('name')->shouldReturn(true);
        $this->isSortedBy('code')->shouldReturn(false);
    }

    function it_throws_exception_when_trying_to_sort_by_a_non_existent_field(Grid $gridDefinition): void
    {
        $gridDefinition->hasField('code')->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('Field "code" does not exist.'))
            ->during('getSortingOrder', ['code'])
        ;
    }

    function it_throws_exception_when_trying_to_sort_by_a_non_sortable_field(
        Grid $gridDefinition,
        Field $nameField
    ): void {
        $gridDefinition->hasField('code')->willReturn(true);

        $gridDefinition->hasField('name')->willReturn(true);
        $gridDefinition->getField('name')->willReturn($nameField);
        $nameField->isSortable()->willReturn(false);

        $gridDefinition->getSorting()->willReturn(['code' => ['order' => 'asc']]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('isSortedBy', ['name'])
        ;
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getSortingOrder', ['name'])
        ;
    }
}
