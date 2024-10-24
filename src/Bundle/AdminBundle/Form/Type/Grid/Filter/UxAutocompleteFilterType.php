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

namespace Sylius\Bundle\AdminBundle\Form\Type\Grid\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField(
    alias: 'sylius_admin_grid_filter_autocomplete',
    route: 'sylius_admin_entity_autocomplete',
)]
final class UxAutocompleteFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('extra_options')
            ->setNormalizer('extra_options', static function (Options $options, array $extraOptions): array {
                if (!isset($extraOptions['class'])) {
                    throw new MissingOptionsException('Missing node "class" within the "extra_options" option.');
                }

                return $extraOptions;
            })
            ->setDefault('class', function (Options $options): string {
                return $options['extra_options']['class'] ?? '';
            })
            ->setDefault('choice_label', function (Options $options, mixed $label): mixed {
                return $options['extra_options']['choice_label'] ?? $label;
            })
        ;
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_ux_autocomplete';
    }
}
