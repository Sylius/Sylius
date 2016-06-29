<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Grid\Filter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Grid\Data\DataSourceInterface;
use Sylius\Grid\Data\ExpressionBuilderInterface;
use Sylius\Grid\Filter\BooleanFilter;
use Sylius\Grid\Filtering\FilterInterface;

/**
 * @mixin BooleanFilter
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BooleanFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Grid\Filter\BooleanFilter');
    }
    
    function it_implements_filter_interface()
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_filters_true_boolean_values(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('enabled', true)->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();
        
        $this->apply($dataSource, 'enabled', BooleanFilter::TRUE, []);
    }

    function it_filters_false_boolean_values(
        DataSourceInterface $dataSource,
        ExpressionBuilderInterface $expressionBuilder
    ) {
        $dataSource->getExpressionBuilder()->willReturn($expressionBuilder);

        $expressionBuilder->equals('enabled', false)->willReturn('EXPR');
        $dataSource->restrict('EXPR')->shouldBeCalled();

        $this->apply($dataSource, 'enabled', BooleanFilter::FALSE, []);
    }
}
