<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\Sorting;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Sorting\Sorter;
use Sylius\Component\Grid\Sorting\SorterInterface;

/**
 * @mixin Sorter
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SorterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Sorter::class);
    }

    function it_implements_grid_data_source_sorter_interface()
    {
        $this->shouldImplement(SorterInterface::class);
    }

    function it_sorts_the_data_source_via_expression_builder_based_on_the_grid_definition(
        Grid $grid,
        Field $nameField,
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $parameters = new Parameters();

        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $grid->getSorting()->willReturn(['name' => 'desc']);
        $grid->hasField('name')->willReturn(true);
        $grid->getField('name')->willReturn($nameField);
        $nameField->isSortable()->willReturn(true);
        $nameField->getSortable()->willReturn('translation.name');

        $expressionBuilder->addOrderBy('translation.name', 'desc')->shouldBeCalled();

        $this->sort($dataSource, $grid, $parameters);
    }

    function it_sorts_the_data_source_via_expression_builder_based_on_sorting_parameter(
        Grid $grid,
        Field $nameField,
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $parameters = new Parameters(['sorting' => ['name' => 'asc']]);

        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $grid->getSorting()->willReturn(['code' => 'desc']);

        $grid->hasField('name')->willReturn(true);
        $grid->getField('name')->willReturn($nameField);
        $nameField->isSortable()->willReturn(true);
        $nameField->getSortable()->willReturn('translation.name');

        $expressionBuilder->addOrderBy('translation.name', 'asc')->shouldBeCalled();

        $this->sort($dataSource, $grid, $parameters);
    }
}
