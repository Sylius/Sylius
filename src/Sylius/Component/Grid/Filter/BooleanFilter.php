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
class BooleanFilter implements FilterInterface
{
    const TRUE  = 'true';
    const FALSE = 'false';

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options)
    {
        $value = $data['value'];

        if (empty($value)) {
            return;
        }

        $field = isset($options['field']) ? $options['field'] : $name;

        $value = self::TRUE === $value;

        $dataSource->restrict($dataSource->getExpressionBuilder()->equals($field, $value));
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
                'field' => array('string')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'boolean';
    }
}
