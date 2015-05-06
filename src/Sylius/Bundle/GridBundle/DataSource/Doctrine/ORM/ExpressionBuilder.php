<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\DataSource\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Grid\DataSource\ExpressionBuilderInterface;

/**
 * Doctrine DataSource.
 *
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
    public function andX($expressions)
    {
        $expr = $this->queryBuilder->expr();

        if (is_array($expressions)) {
            return call_user_func_array(array($expr, 'andX'), $expressions);
        }

        return $this->queryBuilder->expr()->andX(func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function orX($expressions)
    {
        $expr = $this->queryBuilder->expr();

        if (is_array($expressions)) {
            return call_user_func_array(array($expr, 'orX'), $expressions);
        }

        return $this->queryBuilder->expr()->orX(func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function comparison($field, $operator, $value)
    {
        // TODO: Implement comparison() method.
    }

    /**
     * {@inheritdoc}
     */
    public function equals($field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->eq($this->getFieldName($field), ':'.$field);
    }

    /**
     * {@inheritdoc}
     */
    public function notEquals($field, $value)
    {
        $this->queryBuilder->setParameter($field, $value);

        return $this->queryBuilder->expr()->neq($this->getFieldName($field), ':'.$field);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThan($field, $value)
    {
        $this->queryBuilder->andWhere($this->getFieldName($field).' < :'.$field)->setParameter($field, $value);
    }

    public function lessThanOrEqual($field, $value)
    {
        $this->queryBuilder->andWhere($this->getFieldName($field).' =< :'.$field)->setParameter($field, $value);
    }

    public function greaterThan($field, $value)
    {
        $this->queryBuilder->andWhere($this->getFieldName($field).' > :'.$field)->setParameter($field, $value);
    }

    public function greaterThanOrEqual($field, $value)
    {
        $this->queryBuilder->andWhere($this->getFieldName($field).' => :%s'.$field)->setParameter($field, $value);
    }

    public function in($field, array $values)
    {
        return $this->queryBuilder->expr()->in($this->getFieldName($field), $values);
    }

    public function notIn($field, array $values)
    {
        return $this->queryBuilder->expr()->notIn($this->getFieldName($field), $values);
    }

    public function isNull($field)
    {
        return $this->queryBuilder->expr()->isNull($this->getFieldName($field));
    }

    public function isNotNull($field)
    {
        return $this->queryBuilder->expr()->isNotNull($this->getFieldName($field));
    }

    public function like($field, $pattern)
    {
        return $this->queryBuilder->expr()->like($this->getFieldName($field), $this->queryBuilder->expr()->literal($pattern));
    }

    public function notLike($field, $pattern)
    {
        return $this->queryBuilder->expr()->notLike($this->getFieldName($field), $this->queryBuilder->expr()->literal($pattern));
    }

    public function orderBy($field, $direction)
    {
        return $this->queryBuilder->orderBy($this->getFieldName($field), $direction);
    }

    private function getFieldName($field)
    {
        if (false === strpos($field, '.')) {
            return $this->queryBuilder->getRootAlias().'.'.$field;
        }

        return $field;
    }
}
