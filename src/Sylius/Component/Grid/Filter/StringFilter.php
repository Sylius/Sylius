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

namespace Sylius\Component\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class StringFilter implements FilterInterface
{
    public const NAME = 'string';

    public const TYPE_EQUAL = 'equal';
    public const TYPE_NOT_EQUAL = 'not_equal';
    public const TYPE_EMPTY = 'empty';
    public const TYPE_NOT_EMPTY = 'not_empty';
    public const TYPE_CONTAINS = 'contains';
    public const TYPE_NOT_CONTAINS = 'not_contains';
    public const TYPE_STARTS_WITH = 'starts_with';
    public const TYPE_ENDS_WITH = 'ends_with';
    public const TYPE_IN = 'in';
    public const TYPE_NOT_IN = 'not_in';

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options)
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();

        if (is_array($data) && !isset($data['type'])) {
            $data['type'] = isset($options['type']) ? $options['type'] : self::TYPE_CONTAINS;
        }

        if (!is_array($data)) {
            $data = ['type' => self::TYPE_CONTAINS, 'value' => $data];
        }

        $fields = array_key_exists('fields', $options) ? $options['fields'] : [$name];

        $type = $data['type'];
        $value = array_key_exists('value', $data) ? $data['value'] : null;

        if (!in_array($type, [self::TYPE_NOT_EMPTY, self::TYPE_EMPTY], true) && '' === trim($value)) {
            return;
        }

        if (1 === count($fields)) {
            $dataSource->restrict($this->getExpression($expressionBuilder, $type, current($fields), $value));

            return;
        }

        $expressions = [];
        foreach ($fields as $field) {
            $expressions[] = $this->getExpression($expressionBuilder, $type, $field, $value);
        }

        if (self::TYPE_NOT_EQUAL === $type) {
            $dataSource->restrict($expressionBuilder->andX(...$expressions));

            return;
        }

        $dataSource->restrict($expressionBuilder->orX(...$expressions));
    }

    /**
     * @param ExpressionBuilderInterface $expressionBuilder
     * @param string $type
     * @param string $field
     * @param mixed $value
     *
     * @return ExpressionBuilderInterface
     */
    private function getExpression(ExpressionBuilderInterface $expressionBuilder, $type, $field, $value)
    {
        switch ($type) {
            case self::TYPE_EQUAL:
                return $expressionBuilder->equals($field, $value);
            case self::TYPE_NOT_EQUAL:
                return $expressionBuilder->notEquals($field, $value);
            case self::TYPE_EMPTY:
                return $expressionBuilder->isNull($field);
            case self::TYPE_NOT_EMPTY:
                return $expressionBuilder->isNotNull($field);
            case self::TYPE_CONTAINS:
                return $expressionBuilder->like($field, '%'.$value.'%');
            case self::TYPE_NOT_CONTAINS:
                return $expressionBuilder->notLike($field, '%'.$value.'%');
            case self::TYPE_STARTS_WITH:
                return $expressionBuilder->like($field, $value.'%');
            case self::TYPE_ENDS_WITH:
                return $expressionBuilder->like($field, '%'.$value);
            case self::TYPE_IN:
                return $expressionBuilder->in($field, array_map('trim', explode(',', $value)));
            case self::TYPE_NOT_IN:
                return $expressionBuilder->notIn($field, array_map('trim', explode(',', $value)));
            default:
                throw new \InvalidArgumentException(sprintf('Could not get an expression for type "%s"!', $type));
        }
    }
}
