<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Form\Type\Filter;

use Sylius\Component\Grid\Filter\DateTimeFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Datetime filter type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DateTimeFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'choices' => array(
                    DateTimeFilter::TYPE_BETWEEN     => 'between',
                    DateTimeFilter::TYPE_NOT_BETWEEN => 'not_between',
                    DateTimeFilter::TYPE_MORE_THAN   => 'more_than',
                    DateTimeFilter::TYPE_LESS_THAN   => 'less_than'
                )
            ))
            ->add('from', 'datetime', array('required' => false))
            ->add('to', 'datetime', array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => null,
            ))
            ->setOptional(array(
                'field'
            ))
            ->setAllowedTypes(array(
                'field' => array('string')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_filter_datetime';
    }
}
