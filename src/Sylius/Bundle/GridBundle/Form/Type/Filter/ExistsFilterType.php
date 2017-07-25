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

use Sylius\Component\Grid\Filter\ExistsFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ExistsFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => [
                    'sylius.ui.no_label' => ExistsFilter::FALSE,
                    'sylius.ui.yes_label' => ExistsFilter::TRUE,
                ],
                'choice_values' => [
                    ExistsFilter::FALSE,
                    ExistsFilter::TRUE,
                ],
                'data_class' => null,
                'required' => false,
                'placeholder' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_grid_filter_exists';
    }
}
