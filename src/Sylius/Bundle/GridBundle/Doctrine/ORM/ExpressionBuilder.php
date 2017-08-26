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

namespace Sylius\Bundle\GridBundle\Doctrine\ORM;

use Doctrine\ORM\Query\Expr\Comparison;
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
        return new Comparison($field, $operator, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function equals($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        return $this->queryBuilder->expr()->eq($this->getFieldName($field), ':'.$parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function notEquals($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        return $this->queryBuilder->expr()->neq($this->getFieldName($field), ':'.$parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThan($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' < :'.$parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThanOrEqual($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' <= :'.$parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThan($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' > :'.$parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThanOrEqual($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' >= :'.$parameterName);
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
     *
     * @return string
     */
    private function getParameterName($field)
    {
        $parameterName = str_replace('.', '_', $field);

        $i = 1;
        while ($this->hasParameterName($parameterName)) {
            $parameterName .= $i;
        }

        return $parameterName;
    }

    /**
     * @param string $parameterName
     *
     * @return bool
     */
    private function hasParameterName($parameterName)
    {
        return null !== $this->queryBuilder->getParameter($parameterName);
    }
}
