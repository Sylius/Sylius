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

use Sylius\Component\Grid\Filter\StringFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Sylius string filter type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StringFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'choices' => array(
                    StringFilter::TYPE_CONTAINS     => 'contains',
                    StringFilter::TYPE_NOT_CONTAINS => 'not_contains',
                    StringFilter::TYPE_EQUAL        => 'equal',
                    StringFilter::TYPE_EMPTY        => 'empty',
                    StringFilter::TYPE_NOT_EMPTY    => 'not_empty',
                    StringFilter::TYPE_STARTS_WITH  => 'starts_with',
                    StringFilter::TYPE_ENDS_WITH    => 'ends_with',
                    StringFilter::TYPE_IN           => 'in',
                    StringFilter::TYPE_NOT_IN       => 'not_in',
                ),
            ))
            ->add('value', 'text', array('required' => false))
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
    public function getName()
    {
        return 'sylius_filter_string';
    }
}
