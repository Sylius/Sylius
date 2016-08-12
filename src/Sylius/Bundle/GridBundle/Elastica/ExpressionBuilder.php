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

use Sylius\Component\Grid\Data\DataSourceInterface;
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
    public function __construct($query)
    {
        if (empty($query)) {
            $this->query = ['match_all' => []];
        } else {
            $this->initQueryForFilters();
            $this->query['filtered']['filter'] = $query;
        }
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
        if ($this->isNested($field)) {
            $properties = explode('.', $field);

            return ['nested' =>  $properties[0], 'expression' => ['term' => [ $field => $pattern ]]];
        }

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
        if (isset($expression['nested'])) {
            $this->addNestedFilter($expression['nested'], $expression['expression'], $condition);
        } else {
            if (empty($this->query['filtered']['filter'])) {
                $this->query['filtered']['filter'] = $expression;
            } else {
                if (!isset($this->query['filtered']['filter'][$condition])) {
                    $this->query['filtered']['filter'] = [$condition => [
                        $this->query['filtered']['filter'],
                        $expression,
                    ]];
                } else {
                    $this->query['filtered']['filter'][$condition][] = $expression;
                }
            }
        }
    }

    /**
     * @param string $path
     * @param array $expression
     * @param string $condition
     */
    public function addNestedFilter($path, $expression, $condition)
    {
        if (empty($this->query['filtered']['filter'])) {
            $this->query['filtered']['filter']['nested'] = [
                'path' => $path,
                'filter' => [
                    'bool' => [
                        'must' => $expression
                    ]
                ],
            ];
        } else {
            if (!isset($this->query['filtered']['filter'][$condition])) {
                $this->query['filtered']['filter'] = [$condition => [
                    $this->query['filtered']['filter'],
                    ['nested' => [
                        'path' => $path,
                        'filter' => [
                            'bool' => [
                                'must' => $expression
                            ]
                        ]
                    ]],
                ]];
            } else {
                $this->query['filtered']['filter'][$condition][] = ['nested' => [
                    'path' => $path,
                    'filter' => [
                        'bool' => [
                            'must' => $expression
                        ]
                    ]
                ]];
            }
        }
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

    /**
     * @param string $field
     * @return boolean
     */
    private function isNested($field)
    {
        return preg_match('/\./', $field);
    }
}
