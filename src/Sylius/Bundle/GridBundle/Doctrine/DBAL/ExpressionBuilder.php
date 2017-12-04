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

namespace Sylius\Bundle\GridBundle\Doctrine\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

final class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function andX(...$expressions)
    {
        return $this->queryBuilder->expr()->andX(...$expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function orX(...$expressions)
    {
        return $this->queryBuilder->expr()->orX(...$expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function comparison(string $field, string $operator, $value)
    {
        return $this->queryBuilder->expr()->comparison($field, $operator, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(string $field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->eq($field, ':' . $field);
    }

    /**
     * {@inheritdoc}
     */
    public function notEquals(string $field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->neq($field, ':' . $field);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThan(string $field, $value)
    {
        $this->queryBuilder->andWhere($field . ' < :' . $field)->setParameter($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThanOrEqual(string $field, $value)
    {
        $this->queryBuilder->andWhere($field . ' =< :' . $field)->setParameter($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThan(string $field, $value)
    {
        $this->queryBuilder->andWhere($field . ' > :' . $field)->setParameter($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThanOrEqual(string $field, $value)
    {
        $this->queryBuilder->andWhere($field . ' => :%s' . $field)->setParameter($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function in(string $field, array $values)
    {
        return $this->queryBuilder->expr()->in($field, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function notIn(string $field, array $values)
    {
        return $this->queryBuilder->expr()->notIn($field, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function isNull(string $field)
    {
        return $this->queryBuilder->expr()->isNull($field);
    }

    /**
     * {@inheritdoc}
     */
    public function isNotNull(string $field)
    {
        return $this->queryBuilder->expr()->isNotNull($field);
    }

    /**
     * {@inheritdoc}
     */
    public function like(string $field, string $pattern)
    {
        return $this->queryBuilder->expr()->like($field, $this->queryBuilder->expr()->literal($pattern));
    }

    /**
     * {@inheritdoc}
     */
    public function notLike(string $field, string $pattern)
    {
        return $this->queryBuilder->expr()->notLike($field, $this->queryBuilder->expr()->literal($pattern));
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy(string $field, string $direction)
    {
        return $this->queryBuilder->orderBy($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderBy(string $field, string $direction)
    {
        return $this->queryBuilder->addOrderBy($field, $direction);
    }
}
