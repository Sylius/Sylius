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
use Sylius\Component\Grid\Filtering\FilterInterface;

final class DateFilterSpec extends ObjectBehavior
{
    function it_implements_a_filter_interface(): void
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_filters_date_from(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->greaterThanOrEqual('checkoutCompletedAt', '2016-12-05 08:00')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'from' => [
                    'date' => '2016-12-05',
                    'time' => '08:00',
                ],
            ],
            []
        );
    }

    function it_filters_date_from_not_inclusive(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->greaterThan('checkoutCompletedAt', '2016-12-05 08:00')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'from' => [
                    'date' => '2016-12-05',
                    'time' => '08:00',
                ],
                'to' => [
                    'date' => '',
                    'time' => '',
                ],
            ],
            ['inclusive_from' => false]
        );
    }

    function it_filters_date_from_with_default_time(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->greaterThanOrEqual('checkoutCompletedAt', '2016-12-05 00:00')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'from' => [
                    'date' => '2016-12-05',
                    'time' => '',
                ],
                'to' => [
                    'date' => '',
                    'time' => '',
                ],
            ],
            []
        );
    }

    function it_filters_date_to(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->lessThan('checkoutCompletedAt', '2016-12-06 08:00')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'to' => [
                    'date' => '2016-12-06',
                    'time' => '08:00',
                ],
            ],
            []
        );
    }

    function it_filters_date_to_inclusive(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->lessThanOrEqual('checkoutCompletedAt', '2016-12-06 08:00')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'from' => [
                    'date' => '',
                    'time' => '',
                ],
                'to' => [
                    'date' => '2016-12-06',
                    'time' => '08:00',
                ],
            ],
            ['inclusive_to' => true]
        );
    }

    function it_filters_date_to_with_default_time(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->lessThan('checkoutCompletedAt', '2016-12-06 23:59')->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'to' => [
                    'date' => '2016-12-06',
                    'time' => '',
                ],
            ],
            []
        );
    }

    function it_filters_date_from_to(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->greaterThanOrEqual('checkoutCompletedAt', '2016-12-05 08:00')->willReturn('EXPR1');
        $dataSource->restrict('EXPR1')->shouldBeCalled();

        $expressionBuilder->lessThan('checkoutCompletedAt', '2016-12-06 08:00')->willReturn('EXPR2');
        $dataSource->restrict('EXPR2')->shouldBeCalled();

        $this->apply(
            $dataSource,
            'checkoutCompletedAt',
            [
                'from' => [
                    'date' => '2016-12-05',
                    'time' => '08:00',
                ],
                'to' => [
                    'date' => '2016-12-06',
                    'time' => '08:00',
                ],
            ],
            []
        );
    }
}
