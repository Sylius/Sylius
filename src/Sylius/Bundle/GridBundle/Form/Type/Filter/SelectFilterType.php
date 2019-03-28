<?php

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SelectFilterType extends AbstractType
{


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('placeholder', 'sylius.ui.all')
            ->setAllowedTypes('choices', ['array']
            )
        ;
    }

        public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix()
    {
        return 'sylius_grid_filter_select';
    }
}
