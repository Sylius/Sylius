<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\GridBundle\Doctrine\PHPCRODM;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\DataSource;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionBuilder;
use Doctrine\ODM\PHPCR\Query\Builder\ConstraintAndx;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ODM\PHPCR\Query\Builder\ConstraintComparison;
use Doctrine\ODM\PHPCR\Query\Builder\ConstraintOrx;
use Sylius\Component\Grid\Parameters;
use Pagerfanta\Pagerfanta;
use Doctrine\ODM\PHPCR\Query\Builder\OrderBy;
use Doctrine\ODM\PHPCR\Query\Builder\Ordering;

/**
 * @mixin Driver
 */
final class DataSourceSpec extends ObjectBehavior
{
    function let(QueryBuilder $queryBuilder, ExpressionBuilder $expressionBuilder)
    {
        $this->beConstructedWith($queryBuilder, $expressionBuilder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DataSource::class);
    }

    function it_implements_data_source()
    {
        $this->shouldImplement(DataSourceInterface::class);
    }

    function it_should_restrict_with_or_condition(
        Comparison $comparison,
        Value $value,
        QueryBuilder $queryBuilder,
        ConstraintOrx $constraint,
        ConstraintComparison $comparisonConstraint
    )
    {
        $queryBuilder->orWhere()->willReturn($constraint);
        $value->getValue()->willReturn('value');
        $comparison->getValue()->willReturn($value);
        $comparison->getField()->willReturn('foo');
        $comparison->getOperator()->willReturn('=');

        $constraint->eq()->willReturn($comparisonConstraint);
        $comparisonConstraint->field('o.foo')->willReturn($comparisonConstraint);
        $comparisonConstraint->literal('value')->shouldBeCalled()->willReturn($comparisonConstraint);
        $comparisonConstraint->end()->shouldBeCalled();

        $this->restrict($comparison, DataSourceInterface::CONDITION_OR);
    }

    function it_should_throw_an_exception_if_an_unknown_condition_is_passed(
        Comparison $comparison
    )
    {
        $this->shouldThrow(
            new \RuntimeException('Unknown restrict condition "foo"')
        )->during('restrict', [ $comparison, 'foo' ]);
    }

    function it_should_return_the_expression_builder(
        ExpressionBuilder $expressionBuilder
    )
    {
        $this->getExpressionBuilder()->shouldReturn($expressionBuilder);
    }

    function it_should_get_the_data(
        ExpressionBuilder $expressionBuilder,
        Parameters $parameters
    )
    {
        $expressionBuilder->getOrderBys()->willReturn([]);
        $parameters->get('page', 1)->willReturn(1);
        $this->getData($parameters)->shouldHaveType(Pagerfanta::class);
    }

    function it_should_set_the_order_on_the_query_builder(
        QueryBuilder $queryBuilder,
        ExpressionBuilder $expressionBuilder,
        Parameters $parameters,
        OrderBy $orderBy,
        Ordering $ordering
    )
    {
        $expressionBuilder->getOrderBys()->willReturn([
            'foo' => 'asc',
            'bar' => 'desc'
        ]);
        $queryBuilder->orderBy()->willReturn($orderBy);
        $orderBy->asc()->willReturn($ordering);
        $orderBy->desc()->willReturn($ordering);
        $ordering->field('o.foo')->shouldBeCalled();
        $ordering->field('o.bar')->shouldBeCalled();

        $parameters->get('page', 1)->willReturn(1);
        $this->getData($parameters)->shouldHaveType(Pagerfanta::class);
    }

    function it_should_set_the_order_on_the_query_builder_as_fields_only(
        QueryBuilder $queryBuilder,
        ExpressionBuilder $expressionBuilder,
        Parameters $parameters,
        OrderBy $orderBy,
        Ordering $ordering
    )
    {
        $expressionBuilder->getOrderBys()->willReturn([
            'foo',
            'bar',
        ]);
        $queryBuilder->orderBy()->willReturn($orderBy);
        $orderBy->asc()->willReturn($ordering);
        $orderBy->asc()->willReturn($ordering);
        $ordering->field('o.foo')->shouldBeCalled();
        $ordering->field('o.bar')->shouldBeCalled();

        $parameters->get('page', 1)->willReturn(1);
        $this->getData($parameters)->shouldHaveType(Pagerfanta::class);
    }
}
