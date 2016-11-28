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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
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
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'sylius.ui.contains' => StringFilter::TYPE_CONTAINS,
                    'sylius.ui.not_contains' => StringFilter::TYPE_NOT_CONTAINS,
                    'sylius.ui.equal' => StringFilter::TYPE_EQUAL,
                    'sylius.ui.empty' => StringFilter::TYPE_EMPTY,
                    'sylius.ui.not_empty' => StringFilter::TYPE_NOT_EMPTY,
                    'sylius.ui.starts_with' => StringFilter::TYPE_STARTS_WITH,
                    'sylius.ui.ends_with' => StringFilter::TYPE_ENDS_WITH,
                    'sylius.ui.in' => StringFilter::TYPE_IN,
                    'sylius.ui.not_in' => StringFilter::TYPE_NOT_IN,
                ],
            ])
            ->add('value', TextType::class, ['required' => false])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
            ])
            ->setDefined('fields')
            ->setAllowedTypes('fields', 'array')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_grid_filter_string';
    }
}
