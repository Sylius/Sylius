<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\ColumnType;

use Sylius\Component\Grid\Definition\Grid;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StringColumnType extends AbstractColumnType
{
    /**
     * {@inheritdoc}
     */
    public function render($data, $name, array $options = array())
    {
        $value = $this->getPropertyAccessor()->getValue($data, isset($options['path']) ? $options['path'] : $name);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setOptional(array(
                'path'
            ))
            ->setAllowedTypes(array(
                'path' => array('string')
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
}
