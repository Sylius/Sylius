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

namespace spec\Sylius\Component\Core\Grid\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class ResourceAutocompleteFilterSpec extends ObjectBehavior
{
    function it_implements_filter_interface(): void
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_does_nothing_when_data_is_empty(
        DataSourceInterface $dataSource,
    ): void {
        $dataSource->getExpressionBuilder()->shouldNotBeCalled();

        $this->apply($dataSource, 'name', '', []);
    }

    function it_filters_by_value_and_default_field(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('name', 'dress')->willReturn('EXPR1');
        $expressionBuilder->orX('EXPR1')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'name', 'dress', []);
    }

    function it_filters_by_value_and_passed_field(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('code', 'dress')->willReturn('EXPR1');
        $expressionBuilder->orX('EXPR1')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'name', 'dress', ['fields' => ['code']]);
    }

    function it_filters_by_value_and_multiple_fields(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('code', 'dress')->willReturn('EXPR1');
        $expressionBuilder->equals('name', 'dress')->willReturn('EXPR2');
        $expressionBuilder->orX('EXPR1', 'EXPR2')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'name', 'dress', ['fields' => ['code', 'name']]);
    }

    function it_filters_by_comma_separated_values_and_default_field(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('name', 'dress')->willReturn('EXPR1');
        $expressionBuilder->equals('name', 'jeans')->willReturn('EXPR2');
        $expressionBuilder->orX('EXPR1', 'EXPR2')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'name', 'dress,jeans', []);
    }

    function it_filters_by_comma_separated_values_and_passed_field(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('code', 'dress')->willReturn('EXPR1');
        $expressionBuilder->equals('code', 'jeans')->willReturn('EXPR2');
        $expressionBuilder->orX('EXPR1', 'EXPR2')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'name', 'dress,jeans', ['fields' => ['code']]);
    }

    function it_filters_by_comma_separated_values_and_multiple_fields(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder,
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('code', 'dress')->willReturn('CODE1');
        $expressionBuilder->equals('code', 'jeans')->willReturn('CODE2');
        $expressionBuilder->equals('name', 'dress')->willReturn('NAME1');
        $expressionBuilder->equals('name', 'jeans')->willReturn('NAME2');
        $expressionBuilder->orX('CODE1', 'CODE2', 'NAME1', 'NAME2')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'name', 'dress,jeans', ['fields' => ['code', 'name']]);
    }
}
