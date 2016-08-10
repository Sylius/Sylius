<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Elastica;

use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * @var array
     */
    private $query;

    /**
     * @var boolean
     */
    private $filtered = false;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->query = ['match_all' => []];
    }

    /**
     * {@inheritdoc}
     */
    public function andX(...$expressions)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function orX(...$expressions)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function comparison($field, $operator, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function equals($field, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function notEquals($field, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function lessThan($field, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function lessThanOrEqual($field, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThan($field, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThanOrEqual($field, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function in($field, array $values)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function notIn($field, array $values)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function isNull($field)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function isNotNull($field)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function like($field, $pattern)
    {
        return ['term' => [ $field => $pattern ]];
    }

    /**
     * {@inheritdoc}
     */
    public function notLike($field, $pattern)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($field, $direction)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderBy($field, $direction)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * @param array $expression
     * @param string $condition
     */
    public function addFilter($expression, $condition)
    {
        $this->query['filtered']['filter'] = $expression;
        return;
        if (!isset($this->query['filtered']['filter'][$condition])) {
            $this->query['filtered']['filter'][$condition] = [];
        }

        $this->query['filtered']['filter'][$condition] = $expression;
    }

    /**
     * @return array Array that represent elastica raw query
     */
    public function getQuery()
    {
        return $this->query;
    }

    public function initQueryForFilters()
    {
        if (!$this->filtered) {
            $this->query = ['filtered' => ['filter' => []]];
            $this->filtered = true;
        }
    }
}
