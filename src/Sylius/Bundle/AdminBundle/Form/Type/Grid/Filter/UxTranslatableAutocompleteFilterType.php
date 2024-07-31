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

use Sylius\Bundle\AdminBundle\Form\Type\TranslatableAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;

#[AsEntityAutocompleteField(
    alias: 'sylius_admin_grid_filter_translatable_autocomplete',
    route: 'sylius_admin_entity_autocomplete',
)]
final class UxTranslatableAutocompleteFilterType extends AbstractType
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

            // Translatable options passing
            ->setDefault('entity_fields', function (Options $options, array $entityFields) {
                $extraOptions = $options['extra_options'];
                if (!array_key_exists('entity_fields', $extraOptions)) {
                    return $entityFields;
                }

                return $extraOptions['entity_fields'];
            })
            ->setDefault('translation_fields', function (Options $options, array $translationFields) {
                return $options['extra_options']['translation_fields'] ?? $translationFields;
            })
            ->setDefault('locale_code', function (Options $options, string $localeCode) {
                return $options['extra_options']['locale_code'] ?? $localeCode;
            })
        ;
    }

    public function getParent(): string
    {
        return TranslatableAutocompleteType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_ux_translatable_autocomplete';
    }
}
