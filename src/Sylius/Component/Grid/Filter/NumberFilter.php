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

use Sylius\Component\Grid\DataSource\DataSourceInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class NumberFilter implements FilterInterface
{
    const TYPE_GREATER_THAN_OR_EQUAL = 'greater_than_or_equal';
    const TYPE_GREATER_THAN          = 'greater_than';
    const TYPE_LESS_THAN_OR_EQUAL    = 'less_than_or_equal';
    const TYPE_LESS_THAN             = 'less_than';
    const TYPE_EQUAL                 = 'equal';
    const TYPE_NOT_EQUAL             = 'not_equal';
    const TYPE_EMPTY                 = 'empty';
    const TYPE_NOT_EMPTY             = 'not_empty';

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options)
    {
        $value = $data['value'];
        $type = $data['type'];

        if (empty($value) && !in_array($type, array(self::TYPE_EMPTY, self::TYPE_NOT_EMPTY))) {
            return;
        }

        $field = isset($options['field']) ? $options['field'] : $name;
        $expressionBuilder = $dataSource->getExpressionBuilder();

        switch ($type) {
            case self::TYPE_GREATER_THAN_OR_EQUAL:
                $expression = $expressionBuilder->greaterThanOrEqual($field, $value);
            break;
            case self::TYPE_GREATER_THAN:
                $expression = $expressionBuilder->greaterThan($field, $value);
            break;
            case self::TYPE_LESS_THAN_OR_EQUAL:
                $expression = $expressionBuilder->lessThanOrEqual($field, $value);
            break;
            case self::TYPE_LESS_THAN:
                $expression = $expressionBuilder->lessThan($field, $value);
            break;
            case self::TYPE_EQUAL:
                $expression = $expressionBuilder->equals($field, $value);
            break;
            case self::TYPE_NOT_EQUAL:
                $expression = $expressionBuilder->notEquals($field, $value);
            break;
            case self::TYPE_EMPTY:
                $expression = $expressionBuilder->isNull($field);
            break;
            case self::TYPE_NOT_EMPTY:
                $expression = $expressionBuilder->isNotNull($field);
            break;
        }

        $dataSource->restrict($expression);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setOptional(array(
                'field',
            ))
            ->setAllowedTypes(array(
                'field' => array('string'),
            ))
        ;
    }


    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'number';
    }
}
