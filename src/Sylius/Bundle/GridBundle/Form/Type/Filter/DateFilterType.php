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

use Sylius\Component\Grid\Filter\DateFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Date filter type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DateFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'choices' => array(
                    DateFilter::TYPE_BETWEEN     => 'between',
                    DateFilter::TYPE_NOT_BETWEEN => 'not_between',
                    DateFilter::TYPE_MORE_THAN   => 'more_than',
                    DateFilter::TYPE_LESS_THAN   => 'less_than'
                )
            ))
            ->add('from', 'date', array('required' => false))
            ->add('to', 'date', array('required' => false))
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
        return 'sylius_filter_date';
    }
}
