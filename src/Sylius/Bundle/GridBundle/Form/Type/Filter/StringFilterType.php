<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
final class StringFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!isset($options['type'])) {
            $builder
                ->add('type', ChoiceType::class, [
                    'choices' => [
                        'sylius.ui.contains' => StringFilter::TYPE_CONTAINS,
                        'sylius.ui.not_contains' => StringFilter::TYPE_NOT_CONTAINS,
                        'sylius.ui.equal' => StringFilter::TYPE_EQUAL,
                        'sylius.ui.not_equal' => StringFilter::TYPE_NOT_EQUAL,
                        'sylius.ui.empty' => StringFilter::TYPE_EMPTY,
                        'sylius.ui.not_empty' => StringFilter::TYPE_NOT_EMPTY,
                        'sylius.ui.starts_with' => StringFilter::TYPE_STARTS_WITH,
                        'sylius.ui.ends_with' => StringFilter::TYPE_ENDS_WITH,
                        'sylius.ui.in' => StringFilter::TYPE_IN,
                        'sylius.ui.not_in' => StringFilter::TYPE_NOT_IN,
                    ],
                ])
            ;
        }

        $builder
            ->add('value', TextType::class, [
                'required' => false,
                'label' => 'sylius.ui.value',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
            ])
            ->setDefined('type')
            ->setAllowedValues('type', [
                StringFilter::TYPE_CONTAINS,
                StringFilter::TYPE_NOT_CONTAINS,
                StringFilter::TYPE_EQUAL,
                StringFilter::TYPE_NOT_EQUAL,
                StringFilter::TYPE_EMPTY,
                StringFilter::TYPE_NOT_EMPTY,
                StringFilter::TYPE_STARTS_WITH,
                StringFilter::TYPE_ENDS_WITH,
                StringFilter::TYPE_IN,
                StringFilter::TYPE_NOT_IN
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_grid_filter_string';
    }
}
