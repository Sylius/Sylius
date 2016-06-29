<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Grid\Sorting;

use Sylius\Grid\Data\DataSourceInterface;
use Sylius\Grid\Data\ExpressionBuilderInterface;
use Sylius\Grid\Definition\Grid;
use Sylius\Grid\Parameters;
use Sylius\Grid\Sorting\Sorter;
use Sylius\Grid\Sorting\SorterInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Sorter
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SorterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Grid\Sorting\Sorter');
    }
    
    function it_implements_grid_data_source_sorter_interface()
    {
        $this->shouldImplement(SorterInterface::class);
    }
    
    function it_sorts_the_data_source_via_expression_builder_based_on_the_grid_definition(
        Grid $grid, 
        Parameters $parameters, 
        DataSourceInterface $dataSource, 
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $parameters->has('sorting')->willReturn(false);
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);
        $grid->getSorting()->willReturn(['name' => 'desc']);

        $expressionBuilder->addOrderBy('name', 'desc')->shouldBeCalled();

        $this->sort($dataSource, $grid, $parameters);
    }

    function it_sorts_the_data_source_via_expression_builder_based_on_sorting_parameter(
        Grid $grid,
        Parameters $parameters,
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $parameters->has('sorting')->willReturn(true);
        $parameters->get('sorting')->willReturn(['name' => 'asc']);

        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);
        $grid->getSorting()->willReturn(['name' => 'desc']);

        $expressionBuilder->addOrderBy('name', 'asc')->shouldBeCalled();

        $this->sort($dataSource, $grid, $parameters);
    }
}
