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
use Doctrine\Common\Collections\ExpressionBuilder as CollectionsExpressionBuilder;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionBuilder;
use Doctrine\Common\Collections\Expr\Comparison;
use Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExtraComparison;

final class ExpressionBuilderSpec extends ObjectBehavior
{
    function let(CollectionsExpressionBuilder $expressionBuilder)
    {
        $this->beConstructedWith($expressionBuilder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ExpressionBuilder::class);
    }

    function it_builds_andx(
        Comparison $comparison,
        CollectionsExpressionBuilder $expressionBuilder
    )
    {
        $this->andX([$comparison]);
        $expressionBuilder->andX([$comparison])->shouldHaveBeenCalled();
    }

    function it_builds_orx(
        Comparison $comparison,
        CollectionsExpressionBuilder $expressionBuilder
    )
    {
        $this->orX([$comparison]);
        $expressionBuilder->orX([$comparison])->shouldHaveBeenCalled();
    }

    function it_builds_equals(
        CollectionsExpressionBuilder $expressionBuilder
    )
    {
        $this->equals('o.foo', 'value');
        $expressionBuilder->eq('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_not_equals(
        CollectionsExpressionBuilder $expressionBuilder
    )
    {
        $this->notEquals('o.foo', 'value');
        $expressionBuilder->neq('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_less_than_or_equal(
        CollectionsExpressionBuilder $expressionBuilder
    )
    {
        $this->lessThanOrEqual('o.foo', 'value');
        $expressionBuilder->lte('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_greater_than(
        CollectionsExpressionBuilder $expressionBuilder
    )
    {
        $this->greaterThan('o.foo', 'value');
        $expressionBuilder->gt('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_greater_than_or_equal(
        CollectionsExpressionBuilder $expressionBuilder
    )
    {
        $this->greaterThanOrequal('o.foo', 'value');
        $expressionBuilder->gte('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_in(
        CollectionsExpressionBuilder $expressionBuilder
    )
    {
        $this->in('o.foo', ['value']);
        $expressionBuilder->in('o.foo', ['value'])->shouldHaveBeenCalled();
    }

    function it_builds_not_in(
        CollectionsExpressionBuilder $expressionBuilder
    )
    {
        $this->notIn('o.foo', ['value']);
        $expressionBuilder->notIn('o.foo', ['value'])->shouldHaveBeenCalled();
    }

    function it_builds_is_null()
    {
        $expr = $this->isNull('o.foo');
        $expr->getOperator()->shouldReturn(ExtraComparison::IS_NULL);
        $expr->getField()->shouldReturn('o.foo');
    }

    function it_builds_is_not_null()
    {
        $expr = $this->isNotNull('o.foo');
        $expr->getOperator()->shouldReturn(ExtraComparison::IS_NOT_NULL);
        $expr->getField()->shouldReturn('o.foo');
    }

    function it_builds_like(
        CollectionsExpressionBuilder $expressionBuilder
    )
    {
        $this->like('o.foo', 'value');
        $expressionBuilder->contains('o.foo', 'value')->shouldHaveBeenCalled();
    }

    function it_builds_not_like()
    {
        $expr = $this->notLike('o.foo', 'value');
        $expr->getOperator()->shouldReturn(ExtraComparison::NOT_CONTAINS);
        $expr->getField()->shouldReturn('o.foo');
    }

    function it_orders_by()
    {
        $this->orderBy('o.foo', 'asc');
        $this->getOrderBys()->shouldReturn([
            'o.foo' => 'asc',
        ]);
    }

    function it_adds_order_by()
    {
        $this->orderBy('o.foo', 'asc');
        $this->addOrderBy('o.bar', 'desc');
        $this->getOrderBys()->shouldReturn([
            'o.foo' => 'asc',
            'o.bar' => 'desc',
        ]);
    }
}
