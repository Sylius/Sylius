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

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ODM\PHPCR\Query\Builder\ConstraintComparison;
use Doctrine\ODM\PHPCR\Query\Builder\ConstraintOrx;
use Doctrine\ODM\PHPCR\Query\Builder\OrderBy;
use Doctrine\ODM\PHPCR\Query\Builder\Ordering;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\DataSource;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionBuilder;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Parameters;

/**
 * @mixin DataSource
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
    ) {
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
    ) {
        $this->shouldThrow(
            new \RuntimeException('Unknown restrict condition "foo"')
        )->during('restrict', [ $comparison, 'foo' ]);
    }

    function it_should_return_the_expression_builder(
        ExpressionBuilder $expressionBuilder
    ) {
        $this->getExpressionBuilder()->shouldReturn($expressionBuilder);
    }

    function it_should_get_the_data(
        ExpressionBuilder $expressionBuilder
    ) {
        $expressionBuilder->getOrderBys()->willReturn([]);

        $this->getData(new Parameters(['page' => 1]))->shouldHaveType(Pagerfanta::class);
    }

    function it_should_set_the_order_on_the_query_builder(
        QueryBuilder $queryBuilder,
        ExpressionBuilder $expressionBuilder,
        OrderBy $orderBy,
        Ordering $ordering
    ) {
        $expressionBuilder->getOrderBys()->willReturn([
            'foo' => 'asc',
            'bar' => 'desc'
        ]);
        $queryBuilder->orderBy()->willReturn($orderBy);
        $orderBy->asc()->willReturn($ordering);
        $orderBy->desc()->willReturn($ordering);
        $ordering->field('o.foo')->shouldBeCalled();
        $ordering->field('o.bar')->shouldBeCalled();

        $this->getData(new Parameters(['page' => 1]))->shouldHaveType(Pagerfanta::class);
    }

    function it_should_set_the_order_on_the_query_builder_as_fields_only(
        QueryBuilder $queryBuilder,
        ExpressionBuilder $expressionBuilder,
        OrderBy $orderBy,
        Ordering $ordering
    ) {
        $expressionBuilder->getOrderBys()->willReturn([
            'foo',
            'bar',
        ]);
        $queryBuilder->orderBy()->willReturn($orderBy);
        $orderBy->asc()->willReturn($ordering);
        $orderBy->asc()->willReturn($ordering);
        $ordering->field('o.foo')->shouldBeCalled();
        $ordering->field('o.bar')->shouldBeCalled();

        $this->getData(new Parameters(['page' => 1]))->shouldHaveType(Pagerfanta::class);
    }
}
