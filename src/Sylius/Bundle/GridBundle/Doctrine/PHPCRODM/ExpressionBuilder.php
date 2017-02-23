<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Doctrine\PHPCRODM;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\ExpressionBuilder as CollectionsExpressionBuilder;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

/**
 * Creates an object graph (using Doctrine\Commons\Collections\Expr\*) which we
 * can then walk in order to build up the PHPCR-ODM query builder.
 */
class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * @var CollectionsExpressionBuilder
     */
    private $expressionBuilder;

    /**
     * @var array
     */
    private $orderBys = [];

    /**
     * @param CollectionsExpressionBuilder|null $expressionBuilder
     */
    public function __construct(CollectionsExpressionBuilder $expressionBuilder = null)
    {
        $this->expressionBuilder = $expressionBuilder ?: new CollectionsExpressionBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function andX(...$expressions)
    {
        return $this->expressionBuilder->andX(...$expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function orX(...$expressions)
    {
        return $this->expressionBuilder->orX(...$expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function comparison($field, $operator, $value)
    {
        return new Comparison($field, $operator, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function equals($field, $value)
    {
        return $this->expressionBuilder->eq($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function notEquals($field, $value)
    {
        return $this->expressionBuilder->neq($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThan($field, $value)
    {
        return $this->expressionBuilder->lt($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThanOrEqual($field, $value)
    {
        return $this->expressionBuilder->lte($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThan($field, $value)
    {
        return $this->expressionBuilder->gt($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThanOrEqual($field, $value)
    {
        return $this->expressionBuilder->gte($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function in($field, array $values)
    {
        return $this->expressionBuilder->in($field, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function notIn($field, array $values)
    {
        return $this->expressionBuilder->notIn($field, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function isNull($field)
    {
        return new Comparison($field, ExtraComparison::IS_NULL, null);
    }

    /**
     * {@inheritdoc}
     */
    public function isNotNull($field)
    {
        return new Comparison($field, ExtraComparison::IS_NOT_NULL, null);
    }

    /**
     * {@inheritdoc}
     */
    public function like($field, $pattern)
    {
        return $this->expressionBuilder->contains($field, $pattern);
    }

    /**
     * {@inheritdoc}
     */
    public function notLike($field, $pattern)
    {
        return new Comparison($field, ExtraComparison::NOT_CONTAINS, $pattern);
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($field, $direction)
    {
        $this->orderBys = [ $field => $direction ];
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderBy($field, $direction)
    {
        $this->orderBys[$field] = $direction;
    }

    /**
     * @return array
     */
    public function getOrderBys()
    {
        return $this->orderBys;
    }
}
