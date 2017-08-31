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

namespace spec\Sylius\Component\Grid\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filter\StringFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class StringFilterSpec extends ObjectBehavior
{
    function it_implements_filter_interface(): void
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_filters_with_like_by_default(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('firstName', '%John%')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', 'John', []);
    }

    function it_filters_equal_strings(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('firstName', 'John')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_EQUAL, 'value' => 'John'], []);
    }

    function it_filters_not_equal_strings(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->notEquals('firstName', 'John')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_EQUAL, 'value' => 'John'], []);
    }

    function it_filters_data_containing_empty_strings(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->isNull('firstName')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_EMPTY], []);
    }

    function it_filters_data_containing_not_empty_strings(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->isNotNull('firstName')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_EMPTY], []);
    }

    function it_filters_data_containing_a_string(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('firstName', '%John%')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_CONTAINS, 'value' => 'John'], []);
    }

    function it_filters_data_not_containing_a_string(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->notLike('firstName', '%John%')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_CONTAINS, 'value' => 'John'], []);
    }

    function it_filters_data_starting_with_a_string(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('firstName', 'John%')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_STARTS_WITH, 'value' => 'John'], []);
    }

    function it_filters_data_ending_with_a_string(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('firstName', '%John')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_ENDS_WITH, 'value' => 'John'], []);
    }

    function it_filters_data_containing_one_of_strings(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->in('firstName', ['John', 'Paul', 'Rick'])->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_IN, 'value' => 'John, Paul,Rick'], []);
    }

    function it_filters_data_containing_none_of_strings(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->notIn('firstName', ['John', 'Paul', 'Rick'])->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_IN, 'value' => 'John, Paul,Rick'], []);
    }

    function it_filters_in_multiple_fields(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('firstName', '%John%')->willReturn('EXPR1');
        $expressionBuilder->like('lastName', '%John%')->willReturn('EXPR2');
        $expressionBuilder->orX('EXPR1', 'EXPR2')->willReturn('EXPR');

        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'name', 'John', ['fields' => ['firstName', 'lastName']]);
    }

    function it_filters_translation_fields(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('translation.name', '%John%')->willReturn('EXPR');

        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'name', 'John', ['fields' => ['translation.name']]);
    }

    function it_throws_an_exception_if_type_is_unknown(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $this->shouldThrow(\InvalidArgumentException::class)->during('apply', [
            $dataSource,
            'firstName',
            ['type' => 'UNKNOWN_TYPE', 'value' => 'John'],
            [],
        ]);
    }

    function it_ignores_filter_if_its_value_is_empty_and_the_filter_depends_on_it(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_CONTAINS, 'value' => ''], []);
        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_ENDS_WITH, 'value' => ''], []);
        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_EQUAL, 'value' => ''], []);
        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_EQUAL, 'value' => ''], []);
        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_IN, 'value' => ''], []);
        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_CONTAINS, 'value' => ''], []);
        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_NOT_IN, 'value' => ''], []);
        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_STARTS_WITH, 'value' => ''], []);
    }

    function it_does_not_ignore_filter_if_its_value_is_zero(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->like('firstName', '%0%')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'firstName', ['type' => StringFilter::TYPE_CONTAINS, 'value' => '0'], []);
    }
}
