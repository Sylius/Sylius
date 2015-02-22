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
class CountryFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options)
    {
        $value = $data['country'];

        if (empty($value)) {
            return;
        }

        $field = isset($options['field']) ? $options['field'] : $name;
        $expressionBuilder = $dataSource->getExpressionBuilder();

        if ($options['multiple']) {
            $dataSource->restrict($expressionBuilder->in($field, $value));
        } else {
            $dataSource->restrict($expressionBuilder->equals($field, $value));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'multiple' => false
            ))
            ->setOptional(array(
                'field',
            ))
            ->setAllowedTypes(array(
                'field'    => array('string'),
                'multiple' => array('bool')
            ))
        ;
    }


    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'country';
    }
}
