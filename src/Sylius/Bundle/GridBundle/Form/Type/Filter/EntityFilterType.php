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

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class EntityFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idOptions = [
            'class' => $options['class'],
            'label' => false,
            'placeholder' => 'sylius.ui.all',
        ];

        if (null !== $options['choiceLabel']) {
            $idOptions['choice_label'] = $options['choiceLabel'];
        }

        $builder
            ->add('id', EntityType::class, $idOptions)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choiceLabel' => null,
                'class' => null,
            ])
            ->setRequired('class')
            ->setDefined('field')
            ->setAllowedTypes('field', 'string')
            ->setDefined('fields')
            ->setAllowedTypes('fields', 'array')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_grid_filter_entity';
    }
}
