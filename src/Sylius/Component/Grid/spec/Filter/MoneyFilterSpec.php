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
use Prophecy\Argument;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filter\MoneyFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class MoneyFilterSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(MoneyFilter::class);
    }

    function it_implements_filter_interface(): void
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_does_nothing_when_there_is_no_data(DataSourceInterface $dataSource): void
    {
        $this->apply(
            $dataSource,
            'total',
            [],
            ['currency_field' => 'currencyCode']
        );
    }

    function it_filters_by_total_alone_in_all_currencies_when_none_has_been_given(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder
            ->greaterThan('total', 1200)
            ->shouldBeCalledTimes(2)
        ;

        $this->apply(
            $dataSource,
            'total',
            [
                'greaterThan' => '12.00',
                'lessThan' => '',
                'currency' => '',
            ],
            ['currency_field' => 'currencyCode']
        );

        $this->apply(
            $dataSource,
            'total',
            [
                'greaterThan' => '12.00',
                'lessThan' => '',
            ],
            ['currency_field' => 'currencyCode']
        );
    }

    function it_filters_by_given_currency(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('currencyCode', 'GBP')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $expressionBuilder
            ->greaterThan('total', Argument::any())
            ->shouldNotBeCalled()
        ;
        $expressionBuilder
            ->lessThan('total', Argument::any())
            ->shouldNotBeCalled()
        ;

        $this->apply(
            $dataSource,
            'total',
            [
                'currency' => 'GBP',
            ],
            ['currency_field' => 'currencyCode']
        );

        $this->apply(
            $dataSource,
            'total',
            [
                'greaterThan' => '',
                'lessThan' => '',
                'currency' => 'GBP',
            ],
            ['currency_field' => 'currencyCode']
        );
    }

    function it_filters_money_greater_than(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('currencyCode', 'GBP')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $expressionBuilder
            ->greaterThan('total', 1200)
            ->shouldBeCalled()
        ;

        $this->apply(
            $dataSource,
            'total',
            [
                'greaterThan' => '12.00',
                'lessThan' => '',
                'currency' => 'GBP',
            ],
            ['currency_field' => 'currencyCode']
        );
    }

    function it_filters_money_less_than(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('currencyCode', 'GBP')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $expressionBuilder
            ->lessThan('total', 12000)
            ->shouldBeCalled()
        ;

        $this->apply(
            $dataSource,
            'total',
            [
                'greaterThan' => '',
                'lessThan' => '120.00',
                'currency' => 'GBP',
            ],
            ['currency_field' => 'currencyCode']
        );
    }

    function it_filters_money_in_specified_range(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('currencyCode', 'GBP')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $expressionBuilder
            ->greaterThan('total', 1200)
            ->shouldBeCalled()
        ;
        $expressionBuilder
            ->lessThan('total', 12000)
            ->shouldBeCalled()
        ;

        $this->apply(
            $dataSource,
            'total',
            [
                'greaterThan' => '12.00',
                'lessThan' => '120.00',
                'currency' => 'GBP',
            ],
            ['currency_field' => 'currencyCode']
        );
    }

    function its_amount_scale_can_be_configured(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('currencyCode', 'GBP')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $expressionBuilder
            ->greaterThan('total', 1200000)
            ->shouldBeCalled()
        ;
        $expressionBuilder
            ->lessThan('total', 12000000)
            ->shouldBeCalled()
        ;

        $this->apply(
            $dataSource,
            'total',
            [
                'greaterThan' => '12',
                'lessThan' => '120',
                'currency' => 'GBP',
            ],
            [
                'currency_field' => 'currencyCode',
                'scale' => 5,
            ]
        );
    }
}
