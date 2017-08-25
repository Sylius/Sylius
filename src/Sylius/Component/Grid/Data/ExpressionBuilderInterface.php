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

/**
 * @author Paweł Jędrzejewski <pawel@svaluelius.org>
 */
interface ExpressionBuilderInterface
{
    /**
     * @param mixed ...$expressions
     *
     * @return self
     */
    public function andX(...$expressions): self;

    /**
     * @param mixed ...$expressions
     *
     * @return self
     */
    public function orX(...$expressions): self;

    /**
     * @param string $field
     * @param string $operator
     * @param mixed $value
     *
     * @return self
     */
    public function comparison(string $field, string $operator, $value): self;

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function equals(string $field, $value): self;

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function notEquals(string $field, $value): self;

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function lessThan(string $field, $value): self;

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function lessThanOrEqual(string $field, $value): self;

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function greaterThan(string $field, $value): self;

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return self
     */
    public function greaterThanOrEqual(string $field, $value): self;

    /**
     * @param string $field
     * @param array $values
     *
     * @return self
     */
    public function in(string $field, array $values): self;

    /**
     * @param string $field
     * @param array $values
     *
     * @return self
     */
    public function notIn(string $field, array $values): self;

    /**
     * @param string $field
     *
     * @return self
     */
    public function isNull(string $field): self;

    /**
     * @param string $field
     *
     * @return self
     */
    public function isNotNull(string $field): self;

    /**
     * @param string $field
     * @param string $pattern
     *
     * @return self
     */
    public function like(string $field, string $pattern): self;

    /**
     * @param string $field
     * @param string $pattern
     *
     * @return self
     */
    public function notLike(string $field, string $pattern): self;

    /**
     * @param string $field
     * @param string $direction
     *
     * @return self
     */
    public function orderBy(string $field, string $direction): self;

    /**
     * @param string $field
     * @param string $direction
     *
     * @return self
     */
    public function addOrderBy(string $field, string $direction): self;
}
