<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductOptionAutocompleteType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_value' => 'code',
            'choice_name' => 'name',
            'resource' => 'sylius.product_option',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['remote_criteria_type'] = 'contains';
        $view->vars['remote_criteria_name'] = 'phrase';
    }

    public function getParent(): string
    {
        return ResourceAutocompleteChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_product_option_autocomplete_choice';
    }
}
