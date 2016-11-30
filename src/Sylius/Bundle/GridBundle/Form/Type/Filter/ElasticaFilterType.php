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

use Sylius\Component\Grid\Filter\ElasticaFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
class ElasticaFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', [
                'choices' => [
                    ElasticaFilter::TYPE_CONTAINS => 'sylius.ui.contains',
                    ElasticaFilter::TYPE_NOT_CONTAINS => 'sylius.ui.not_contains',
                    ElasticaFilter::TYPE_EQUAL => 'sylius.ui.equal',
                    ElasticaFilter::TYPE_NOT_EQUAL => 'sylius.ui.not_equal',
                    ElasticaFilter::TYPE_EMPTY => 'sylius.ui.empty',
                    ElasticaFilter::TYPE_NOT_EMPTY => 'sylius.ui.not_empty',
                    ElasticaFilter::TYPE_STARTS_WITH => 'sylius.ui.starts_with',
                    ElasticaFilter::TYPE_ENDS_WITH => 'sylius.ui.ends_with',
                    ElasticaFilter::TYPE_IN => 'sylius.ui.in',
                    ElasticaFilter::TYPE_NOT_IN => 'sylius.ui.not_in'
                ]
            ])
            ->add('value', 'text', ['required' => false])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => null
            ])
            ->setOptional([
                'fields'
            ])
            ->setAllowedTypes([
                'fields' => ['array']
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_grid_filter_elastica';
    }
}
