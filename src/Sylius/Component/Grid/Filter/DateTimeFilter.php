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
class DateTimeFilter implements FilterInterface
{
    const TYPE_BETWEEN     = 'between';
    const TYPE_NOT_BETWEEN = 'not_between';
    const TYPE_MORE_THAN   = 'more_than';
    const TYPE_LESS_THAN   = 'less_than';

    /**
     * {@inheritdoc}
     */
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options)
    {
        $value = $data['value'];
        $type = $data['type'];

        if (empty($value)) {
            return;
        }

        $field = isset($options['field']) ? $options['field'] : $name;

        switch ($data['type']) {
            case self::TYPE_BETWEEN:
                $dataSource->equals($field, $value);
            break;
        }
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
        return 'datetime';
    }
}
