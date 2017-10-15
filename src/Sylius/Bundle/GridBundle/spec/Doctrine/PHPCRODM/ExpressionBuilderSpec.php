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
use Doctrine\Common\Collections\ExpressionBuilder as CollectionsExpressionBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExtraComparison;

/**
 * @require Doctrine\ODM\PHPCR\DocumentManagerInterface
 */
final class ExpressionBuilderSpec extends ObjectBehavior
{
    function let(CollectionsExpressionBuilder $expressionBuilder): void
    {
        $this->beConstructedWith($expressionBuilder);
    }

    function it_builds_andx(
        Comparison $comparison,
        CollectionsExpressionBuilder $expressionBuilder
    ): void {
        $this->andX([$comparison]);
        $expressionBuilder->andX([$comparison])->shouldHaveBeenCalled();
    }

    function it_builds_orx(
        Comparison $comparison,
        CollectionsExpressionBuilder $expressionBuilder
    ): void {
        $this->orX([$comparison]);
        $expressionBuilder->orX([$comparison])->shouldHaveBeenCalled();
    }

    function it_builds_equals(
        CollectionsExpressionBuilder $expressionBuilder
    ): void {
        $this->equals('o.foo', 'value');
        $expressionBuilder->eq('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_not_equals(
        CollectionsExpressionBuilder $expressionBuilder
    ): void {
        $this->notEquals('o.foo', 'value');
        $expressionBuilder->neq('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_less_than_or_equal(
        CollectionsExpressionBuilder $expressionBuilder
    ): void {
        $this->lessThanOrEqual('o.foo', 'value');
        $expressionBuilder->lte('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_greater_than(
        CollectionsExpressionBuilder $expressionBuilder
    ): void {
        $this->greaterThan('o.foo', 'value');
        $expressionBuilder->gt('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_greater_than_or_equal(
        CollectionsExpressionBuilder $expressionBuilder
    ): void {
        $this->greaterThanOrequal('o.foo', 'value');
        $expressionBuilder->gte('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_in(
        CollectionsExpressionBuilder $expressionBuilder
    ): void {
        $this->in('o.foo', ['value']);
        $expressionBuilder->in('o.foo', ['value'])->shouldHaveBeenCalled();
    }

    function it_builds_not_in(
        CollectionsExpressionBuilder $expressionBuilder
    ): void {
        $this->notIn('o.foo', ['value']);
        $expressionBuilder->notIn('o.foo', ['value'])->shouldHaveBeenCalled();
    }

    function it_builds_is_null(): void
    {
        $expr = $this->isNull('o.foo');
        $expr->getOperator()->shouldReturn(ExtraComparison::IS_NULL);
        $expr->getField()->shouldReturn('o.foo');
    }

    function it_builds_is_not_null(): void
    {
        $expr = $this->isNotNull('o.foo');
        $expr->getOperator()->shouldReturn(ExtraComparison::IS_NOT_NULL);
        $expr->getField()->shouldReturn('o.foo');
    }

    function it_builds_like(
        CollectionsExpressionBuilder $expressionBuilder
    ): void {
        $this->like('o.foo', 'value');
        $expressionBuilder->contains('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_not_like(): void
    {
        $expr = $this->notLike('o.foo', 'value');
        $expr->getOperator()->shouldReturn(ExtraComparison::NOT_CONTAINS);
        $expr->getField()->shouldReturn('o.foo');
    }

    function it_orders_by(): void
    {
        $this->orderBy('o.foo', 'asc');
        $this->getOrderBys()->shouldReturn([
            'o.foo' => 'asc',
        ]);
    }

    function it_adds_order_by(): void
    {
        $this->orderBy('o.foo', 'asc');
        $this->addOrderBy('o.bar', 'desc');
        $this->getOrderBys()->shouldReturn([
            'o.foo' => 'asc',
            'o.bar' => 'desc',
        ]);
    }
}
