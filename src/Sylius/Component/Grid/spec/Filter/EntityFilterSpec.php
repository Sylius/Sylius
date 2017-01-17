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
use Sylius\Component\Grid\Filter\EntityFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class EntityFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(EntityFilter::class);
    }

    function it_implements_a_filter_interface()
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_gets_type()
    {
        $this->getType()->shouldReturn('entity');
    }

    function it_filters_by_id(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('entity', '7')->willReturn('EXPR1');
        $expressionBuilder->orX('EXPR1')->willReturn('EXPR');

        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'entity', '7', []);
    }

    function it_does_not_filters_when_data_id_is_not_defined(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('entity', Argument::any())->shouldNotBeCalled();
        $dataSource->restrict(Argument::any())->shouldNotBeCalled();

        $this->apply($dataSource, 'entity', '', []);
    }
}
