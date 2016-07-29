<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Doctrine\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    function __construct(QueryBuilder $queryBuilder)
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
    public function comparison($field, $operator, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
        // TODO: Implement comparison() method.
    }

    /**
     * {@inheritdoc}
     */
    public function equals($field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->eq($field, ':'.$field);
    }

    /**
     * {@inheritdoc}
     */
    public function notEquals($field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->neq($field, ':'.$field);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThan($field, $value)
    {
        $this->queryBuilder->andWhere($field.' < :'.$field)->setParameter($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThanOrEqual($field, $value)
    {
        $this->queryBuilder->andWhere($field.' =< :'.$field)->setParameter($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThan($field, $value)
    {
        $this->queryBuilder->andWhere($field.' > :'.$field)->setParameter($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThanOrEqual($field, $value)
    {
        $this->queryBuilder->andWhere($field.' => :%s'.$field)->setParameter($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function in($field, array $values)
    {
        return $this->queryBuilder->expr()->in($field, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function notIn($field, array $values)
    {
        return $this->queryBuilder->expr()->notIn($field, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function isNull($field)
    {
        return $this->queryBuilder->expr()->isNull($field);
    }

    /**
     * {@inheritdoc}
     */
    public function isNotNull($field)
    {
        return $this->queryBuilder->expr()->isNotNull($field);
    }

    /**
     * {@inheritdoc}
     */
    public function like($field, $pattern)
    {
        return $this->queryBuilder->expr()->like($field, $this->queryBuilder->expr()->literal($pattern));
    }

    /**
     * {@inheritdoc}
     */
    public function notLike($field, $pattern)
    {
        return $this->queryBuilder->expr()->notLike($field, $this->queryBuilder->expr()->literal($pattern));
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($field, $direction)
    {
        return $this->queryBuilder->orderBy($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderBy($field, $direction)
    {
        return $this->queryBuilder->addOrderBy($field, $direction);
    }
}
