<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\Filter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filter\StringFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

/**
 * @mixin StringFilter
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StringFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\Filter\StringFilter');
    }
    
    function it_implements_filter_interface()
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_filters_with_like_by_default(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('firstName', '%John%')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();
        
        $this->apply($dataSource, 'firstName', 'John', []);
    }

    function it_filters_equal_strings(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('firstName', 'John')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_EQUAL, 'value' => 'John'], []);
    }

    function it_filters_data_containing_empty_strings(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    )
    {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->isNull('firstName')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_EMPTY], []);
    }

    function it_filters_data_containing_not_empty_strings(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->isNotNull('firstName')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_EMPTY], []);
    }

    function it_filters_data_containing_a_string(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    )
    {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('firstName', '%John%')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_CONTAINS, 'value' => 'John'], []);
    }

    function it_filters_data_not_containing_a_string(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->notLike('firstName', '%John%')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_CONTAINS, 'value' => 'John'], []);
    }

    function it_filters_data_starting_with_a_string(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('firstName', 'John%')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_STARTS_WITH, 'value' => 'John'], []);
    }

    function it_filters_data_ending_with_a_string(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('firstName', '%John')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_ENDS_WITH, 'value' => 'John'], []);
    }

    function it_filters_data_containing_one_of_strings(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->in('firstName', ['John', 'Paul', 'Rick'])->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_IN, 'value' => 'John, Paul,Rick'], []);
    }

    function it_filters_data_containing_none_of_strings(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->notIn('firstName', ['John', 'Paul', 'Rick'])->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_IN, 'value' => 'John, Paul,Rick'], []);
    }

    function it_filters_in_multiple_fields(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('firstName', '%John%')->willReturn('EXPR1');
        $expressionBuilder->like('lastName', '%John%')->willReturn('EXPR2');
        $expressionBuilder->orX(['EXPR1', 'EXPR2'])->willReturn('EXPR');

        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'name', 'John', ['fields' => ['firstName', 'lastName']]);
    }
}
