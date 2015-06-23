<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\DataSource;

/**
 * @author Paweł Jędrzejewski <pawel@svaluelius.org>
 */
interface ExpressionBuilderInterface
{
    /**
     * @param $expressions
     *
     * @return mixed
     */
    public function andX($expressions);

    /**
     * @param $expressions
     *
     * @return mixed
     */
    public function orX($expressions);

    /**
     * @param string $field
     * @param string $operator
     * @param mixed  $value
     *
     * @return mixed
     */
    public function comparison($field, $operator, $value);

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return mixed
     */
    public function equals($field, $value);

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return mixed
     */
    public function notEquals($field, $value);

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return mixed
     */
    public function lessThan($field, $value);

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return mixed
     */
    public function lessThanOrEqual($field, $value);

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return mixed
     */
    public function greaterThan($field, $value);

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return mixed
     */
    public function greaterThanOrEqual($field, $value);

    /**
     * @param string $field
     * @param array  $values
     *
     * @return mixed
     */
    public function in($field, array $values);

    /**
     * @param string $field
     * @param array  $values
     *
     * @return mixed
     */
    public function notIn($field, array $values);

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function isNull($field);

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function isNotNull($field);

    /**
     * @param string $field
     * @param $pattern
     *
     * @return mixed
     */
    public function like($field, $pattern);

    /**
     * @param string $field
     * @param $pattern
     *
     * @return mixed
     */
    public function notLike($field, $pattern);

    /**
     * @param string $field
     * @param $direction
     *
     * @return mixed
     */
    public function orderBy($field, $direction);
}
