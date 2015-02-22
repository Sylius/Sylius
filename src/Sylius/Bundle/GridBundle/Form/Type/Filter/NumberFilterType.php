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

use Sylius\Component\Grid\Filter\NumberFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Sylius number filter type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class NumberFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'choices' => array(
                    NumberFilter::TYPE_GREATER_THAN_OR_EQUAL => '>=',
                    NumberFilter::TYPE_GREATER_THAN          => '>',
                    NumberFilter::TYPE_LESS_THAN_OR_EQUAL    => '=<',
                    NumberFilter::TYPE_LESS_THAN             => '<',
                    NumberFilter::TYPE_EQUAL                 => '=',
                    NumberFilter::TYPE_NOT_EQUAL             => '!=',
                    NumberFilter::TYPE_EMPTY                 => 'empty',
                    NumberFilter::TYPE_NOT_EMPTY             => 'not_empty',
                )
            ))
            ->add('value', 'number', array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => null
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_filter_number';
    }
}
