<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Data;

/**
 * @author Paweł Jędrzejewski <pawel@svaluelius.org>
 */
interface ExpressionBuilderInterface
{
    /**
     * @param mixed $expressions
     *
     * @return self
     */
    public function andX($expressions);

    /**
     * @param mixed $expressions
     *
     * @return self
     */
    public function orX($expressions);

    /**
     * @param string $field
     * @param string $operator
     * @param mixed $value
     *
     * @return self
     */
    public function comparison($field, $operator, $value);

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function equals($field, $value);

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function notEquals($field, $value);

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function lessThan($field, $value);

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function lessThanOrEqual($field, $value);

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function greaterThan($field, $value);

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function greaterThanOrEqual($field, $value);

    /**
     * @param string $field
     * @param array $values
     *
     * @return self
     */
    public function in($field, array $values);

    /**
     * @param string $field
     * @param array $values
     *
     * @return self
     */
    public function notIn($field, array $values);

    /**
     * @param string $field
     *
     * @return self
     */
    public function isNull($field);

    /**
     * @param string $field
     *
     * @return self
     */
    public function isNotNull($field);

    /**
     * @param string $field
     * @param string $pattern
     *
     * @return self
     */
    public function like($field, $pattern);

    /**
     * @param string $field
     * @param string $pattern
     *
     * @return self
     */
    public function notLike($field, $pattern);

    /**
     * @param string $field
     * @param string $direction
     *
     * @return self
     */
    public function orderBy($field, $direction);

    /**
     * @param string $field
     * @param string $direction
     *
     * @return self
     */
    public function addOrderBy($field, $direction);
}
