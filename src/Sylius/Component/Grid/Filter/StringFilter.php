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
use Sylius\Component\Grid\DataSource\ExpressionBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StringFilter implements FilterInterface
{
    const TYPE_EQUAL        = 'equal';
    const TYPE_EMPTY        = 'empty';
    const TYPE_NOT_EMPTY    = 'not_empty';
    const TYPE_CONTAINS     = 'contains';
    const TYPE_NOT_CONTAINS = 'not_contains';
    const TYPE_STARTS_WITH  = 'starts_with';
    const TYPE_ENDS_WITH    = 'ends_with';
    const TYPE_IN           = 'in';
    const TYPE_NOT_IN       = 'not_in';

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

        $fields = isset($options['fields']) ? $options['fields'] : array($name);
        $expressionBuilder = $dataSource->getExpressionBuilder();


        if (1 === count($fields)) {
            $expression = $this->getExpression($expressionBuilder, $type, $fields[0], $value);
        } else {
            $expressions = array();

            foreach ($fields as $field) {
                $expressions[] = $this->getExpression($expressionBuilder, $type, $field, $value);
            }

            if (in_array($type, array(self::TYPE_NOT_CONTAINS, self::TYPE_NOT_IN))) {
                $expression = $expressionBuilder->andX($expressions);
            } else {
                $expression = $expressionBuilder->orX($expressions);
            }
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
                'fields',
            ))
            ->setAllowedTypes(array(
                'fields' => array('array'),
            ))
        ;
    }


    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'string';
    }

    /**
     * Get expression.
     *
     * @param string $type
     * @param string $field
     * @param mixed  $value
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
