<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
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
    public function __construct(QueryBuilder $queryBuilder)
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
            return call_user_func_array([$expr, 'andX'], $expressions);
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
            return call_user_func_array([$expr, 'orX'], $expressions);
        }

        return $this->queryBuilder->expr()->orX(func_get_args());
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
        $this->setParameter($field, $value);

        return $this->queryBuilder->expr()->eq($this->getFieldName($field), ':'.$field);
    }

    /**
     * {@inheritdoc}
     */
    public function notEquals($field, $value)
    {
        $this->setParameter($field, $value);

        return $this->queryBuilder->expr()->neq($this->getFieldName($field), ':'.$field);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThan($field, $value)
    {
        $this->setParameter($field, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' < :'.$field);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThanOrEqual($field, $value)
    {
        $this->setParameter($field, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' =< :'.$field);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThan($field, $value)
    {
        $this->setParameter($field, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' > :'.$field);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThanOrEqual($field, $value)
    {
        $this->setParameter($field, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' => :%s'.$field);
    }

    /**
     * {@inheritdoc}
     */
    public function in($field, array $values)
    {
        return $this->queryBuilder->expr()->in($this->getFieldName($field), $values);
    }

    /**
     * {@inheritdoc}
     */
    public function notIn($field, array $values)
    {
        return $this->queryBuilder->expr()->notIn($this->getFieldName($field), $values);
    }

    /**
     * {@inheritdoc}
     */
    public function isNull($field)
    {
        return $this->queryBuilder->expr()->isNull($this->getFieldName($field));
    }

    /**
     * {@inheritdoc}
     */
    public function isNotNull($field)
    {
        return $this->queryBuilder->expr()->isNotNull($this->getFieldName($field));
    }

    /**
     * {@inheritdoc}
     */
    public function like($field, $pattern)
    {
        return $this->queryBuilder->expr()->like($this->getFieldName($field), $this->queryBuilder->expr()->literal($pattern));
    }

    /**
     * {@inheritdoc}
     */
    public function notLike($field, $pattern)
    {
        return $this->queryBuilder->expr()->notLike($this->getFieldName($field), $this->queryBuilder->expr()->literal($pattern));
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($field, $direction)
    {
        return $this->queryBuilder->orderBy($this->getFieldName($field), $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderBy($field, $direction)
    {
        return $this->queryBuilder->addOrderBy($this->getFieldName($field), $direction);
    }

    /**
     * {@inheritdoc}
     */
    private function getFieldName($field)
    {
        if (false === strpos($field, '.')) {
            return $this->queryBuilder->getRootAlias().'.'.$field;
        }

        return $field;
    }

    /**
     * @param string $field
     * @param mixed $value
     */
    private function setParameter($field, $value)
    {
        $parameterName = str_replace('.', '_', $field);
        $this->queryBuilder->setParameter($parameterName, $value);
    }
}
