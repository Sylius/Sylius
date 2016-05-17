<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Filter;

use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StringFilter implements FilterInterface
{
    const NAME = 'string';

    const TYPE_EQUAL = 'equal';
    const TYPE_EMPTY = 'empty';
    const TYPE_NOT_EMPTY = 'not_empty';
    const TYPE_CONTAINS = 'contains';
    const TYPE_NOT_CONTAINS = 'not_contains';
    const TYPE_STARTS_WITH = 'starts_with';
    const TYPE_ENDS_WITH = 'ends_with';
    const TYPE_IN = 'in';
    const TYPE_NOT_IN = 'not_in';

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options)
    {
        $expressionBuilder = $dataSource->getExpressionBuilder();

        if (!is_array($data)) {
            $data = ['type' => self::TYPE_CONTAINS, 'value' => $data];
        }

        $fields = array_key_exists('fields', $options) ? $options['fields'] : [$name];

        $type = $data['type'];
        $value = array_key_exists('value', $data) ? $data['value'] : null;

        if (1 === count($fields)) {
            $expression = $this->getExpression($expressionBuilder, $type, $fields[0], $value);
        } else {
            $expressions = [];

            foreach ($fields as $field) {
                $expressions[] = $this->getExpression($expressionBuilder, $type, $field, $value);
            }

            $expression = $expressionBuilder->orX($expressions);
        }

        $dataSource->restrict($expression);
    }


    /**
     * @param ExpressionBuilderInterface $expressionBuilder
     * @param string $type
     * @param string $field
     * @param mixed  $value
     * 
     * @return static
     */
    private function getExpression(ExpressionBuilderInterface $expressionBuilder, $type, $field, $value)
    {
        switch ($type) {
            case self::TYPE_EQUAL:
                return $expressionBuilder->equals($field, $value);
                break;
            case self::TYPE_EMPTY:
                return $expressionBuilder->isNull($field);
                break;
            case self::TYPE_NOT_EMPTY:
                return $expressionBuilder->isNotNull($field);
                break;
            case self::TYPE_CONTAINS:
                return $expressionBuilder->like($field, '%'.$value.'%');
                break;
            case self::TYPE_NOT_CONTAINS:
                return $expressionBuilder->notLike($field, '%'.$value.'%');
                break;
            case self::TYPE_STARTS_WITH:
                return $expressionBuilder->like($field, $value.'%');
                break;
            case self::TYPE_ENDS_WITH:
                return $expressionBuilder->like($field, '%'.$value);
                break;
            case self::TYPE_IN:
                return $expressionBuilder->in($field, array_map('trim', explode(',', $value)));
                break;
            case self::TYPE_NOT_IN:
                return $expressionBuilder->notIn($field, array_map('trim', explode(',', $value)));
                break;
        }
    }
}
