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

namespace Sylius\Bundle\GridBundle\Doctrine\PHPCRODM;

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\ExpressionBuilder as CollectionsExpressionBuilder;

@trigger_error(sprintf('The "%s" class is deprecated since Sylius 1.3. Doctrine MongoDB and PHPCR support will no longer be supported in Sylius 2.0.', ExpressionBuilder::class), \E_USER_DEPRECATED);

/**
 * Creates an object graph (using Doctrine\Commons\Collections\Expr\*) which we
 * can then walk in order to build up the PHPCR-ODM query builder.
 */
final class ExpressionBuilder implements ExpressionBuilderInterface
{
    /** @var CollectionsExpressionBuilder */
    private $expressionBuilder;

    /** @var array */
    private $orderBys = [];

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
    public function comparison(string $field, string $operator, $value)
    {
        return new Comparison($field, $operator, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(string $field, $value)
    {
        return $this->expressionBuilder->eq($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function notEquals(string $field, $value)
    {
        return $this->expressionBuilder->neq($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThan(string $field, $value)
    {
        return $this->expressionBuilder->lt($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThanOrEqual(string $field, $value)
    {
        return $this->expressionBuilder->lte($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThan(string $field, $value)
    {
        return $this->expressionBuilder->gt($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThanOrEqual(string $field, $value)
    {
        return $this->expressionBuilder->gte($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function in(string $field, array $values)
    {
        return $this->expressionBuilder->in($field, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function notIn(string $field, array $values)
    {
        return $this->expressionBuilder->notIn($field, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function isNull(string $field)
    {
        return new Comparison($field, ExtraComparison::IS_NULL, null);
    }

    /**
     * {@inheritdoc}
     */
    public function isNotNull(string $field)
    {
        return new Comparison($field, ExtraComparison::IS_NOT_NULL, null);
    }

    /**
     * {@inheritdoc}
     */
    public function like(string $field, string $pattern)
    {
        return $this->expressionBuilder->contains($field, $pattern);
    }

    /**
     * {@inheritdoc}
     */
    public function notLike(string $field, string $pattern)
    {
        return new Comparison($field, ExtraComparison::NOT_CONTAINS, $pattern);
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy(string $field, string $direction)
    {
        $this->orderBys = [$field => $direction];
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderBy(string $field, string $direction)
    {
        $this->orderBys[$field] = $direction;
    }

    public function getOrderBys(): array
    {
        return $this->orderBys;
    }
}
