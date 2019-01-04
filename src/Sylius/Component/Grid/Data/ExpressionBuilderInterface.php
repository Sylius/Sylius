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

namespace Sylius\Component\Grid\Data;

interface ExpressionBuilderInterface
{
    /**
     * @param mixed ...$expressions
     */
    public function andX(...$expressions);

    /**
     * @param mixed ...$expressions
     */
    public function orX(...$expressions);

    public function comparison(string $field, string $operator, $value);

    public function equals(string $field, $value);

    public function notEquals(string $field, $value);

    public function lessThan(string $field, $value);

    public function lessThanOrEqual(string $field, $value);

    public function greaterThan(string $field, $value);

    public function greaterThanOrEqual(string $field, $value);

    public function in(string $field, array $values);

    public function notIn(string $field, array $values);

    public function isNull(string $field);

    public function isNotNull(string $field);

    public function like(string $field, string $pattern);

    public function notLike(string $field, string $pattern);

    public function orderBy(string $field, string $direction);

    public function addOrderBy(string $field, string $direction);
}
