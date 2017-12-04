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

namespace spec\Sylius\Bundle\GridBundle\Doctrine\PHPCRODM;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\ODM\PHPCR\Query\Builder\ConstraintComparison;
use Doctrine\ODM\PHPCR\Query\Builder\ConstraintOrx;
use Doctrine\ODM\PHPCR\Query\Builder\OrderBy;
use Doctrine\ODM\PHPCR\Query\Builder\Ordering;
use Doctrine\ODM\PHPCR\Query\Builder\QueryBuilder;
use Doctrine\ODM\PHPCR\Query\Query;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionBuilderInterface;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Parameters;

/**
 * @require Doctrine\ODM\PHPCR\DocumentManagerInterface
 */
final class DataSourceSpec extends ObjectBehavior
{
    function let(QueryBuilder $queryBuilder, ExpressionBuilderInterface $expressionBuilder): void
    {
        $this->beConstructedWith($queryBuilder, $expressionBuilder);
    }

    function it_implements_data_source(): void
    {
        $this->shouldImplement(DataSourceInterface::class);
    }

    function it_should_restrict_with_or_condition(
        Comparison $comparison,
        Value $value,
        QueryBuilder $queryBuilder,
        ConstraintOrx $constraint,
        ConstraintComparison $comparisonConstraint
    ): void {
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
    ): void {
        $this->shouldThrow(
            new \RuntimeException('Unknown restrict condition "foo"')
        )->during('restrict', [$comparison, 'foo']);
    }

    function it_should_return_the_expression_builder(
        ExpressionBuilderInterface $expressionBuilder
    ): void {
        $this->getExpressionBuilder()->shouldReturn($expressionBuilder);
    }

    function it_should_get_the_data(
        QueryBuilder $queryBuilder,
        ExpressionBuilderInterface $expressionBuilder,
        Query $query
    ): void {
        $expressionBuilder->getOrderBys()->willReturn([]);

        $queryBuilder->orderBy()->willReturn(null);
        $queryBuilder->getQuery()->willReturn($query);
        $query->setMaxResults(Argument::any())->willReturn($query);
        $query->setFirstResult(Argument::any())->willReturn($query);
        $query->execute()->willReturn([]);

        $this->getData(new Parameters(['page' => 1]))->shouldHaveType(Pagerfanta::class);
    }

    function it_should_set_the_order_on_the_query_builder(
        QueryBuilder $queryBuilder,
        ExpressionBuilderInterface $expressionBuilder,
        Query $query,
        OrderBy $orderBy,
        Ordering $ordering
    ): void {
        $expressionBuilder->getOrderBys()->willReturn([
            'foo' => 'asc',
            'bar' => 'desc',
        ]);
        $queryBuilder->orderBy()->willReturn($orderBy);
        $orderBy->asc()->willReturn($ordering);
        $orderBy->desc()->willReturn($ordering);
        $ordering->field('o.foo')->shouldBeCalled();
        $ordering->field('o.bar')->shouldBeCalled();

        $queryBuilder->getQuery()->willReturn($query);
        $query->setMaxResults(Argument::any())->willReturn($query);
        $query->setFirstResult(Argument::any())->willReturn($query);
        $query->execute()->willReturn([]);

        $this->getData(new Parameters(['page' => 1]))->shouldHaveType(Pagerfanta::class);
    }

    function it_should_set_the_order_on_the_query_builder_as_fields_only(
        QueryBuilder $queryBuilder,
        ExpressionBuilderInterface $expressionBuilder,
        Query $query,
        OrderBy $orderBy,
        Ordering $ordering
    ): void {
        $expressionBuilder->getOrderBys()->willReturn([
            'foo',
            'bar',
        ]);
        $queryBuilder->orderBy()->willReturn($orderBy);
        $orderBy->asc()->willReturn($ordering);
        $orderBy->asc()->willReturn($ordering);
        $ordering->field('o.foo')->shouldBeCalled();
        $ordering->field('o.bar')->shouldBeCalled();

        $queryBuilder->getQuery()->willReturn($query);
        $query->setMaxResults(Argument::any())->willReturn($query);
        $query->setFirstResult(Argument::any())->willReturn($query);
        $query->execute()->willReturn([]);

        $this->getData(new Parameters(['page' => 1]))->shouldHaveType(Pagerfanta::class);
    }
}
