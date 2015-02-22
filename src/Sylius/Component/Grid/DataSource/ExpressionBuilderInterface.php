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
    public function andX($expressions);
    public function orX($expressions);
    public function comparison($field, $operator, $value);
    public function equals($field, $value);
    public function notEquals($field, $value);
    public function lessThan($field, $value);
    public function lessThanOrEqual($field, $value);
    public function greaterThan($field, $value);
    public function greaterThanOrEqual($field, $value);
    public function in($field, array $values);
    public function notIn($field, array $values);
    public function isNull($field);
    public function isNotNull($field);
    public function like($field, $pattern);
    public function notLike($field, $pattern);
    public function orderBy($field, $direction);
}
