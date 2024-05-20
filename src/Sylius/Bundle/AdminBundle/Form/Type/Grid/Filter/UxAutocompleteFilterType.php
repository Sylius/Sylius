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

#[AsEntityAutocompleteField(route: 'sylius_admin_entity_autocomplete_admin')]
final class UxAutocompleteFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('extra_options')
            ->setDefault('class', function (Options $options): string {
                return $options['extra_options']['class'] ?? '';
            })
            ->setNormalizer('extra_options', static function (Options $options, array $extraOptions): array {
                if (!isset($extraOptions['class'])) {
                    throw new MissingOptionsException('Missing node "class" within the "extra_options" option.');
                }

                return $extraOptions;
            })
        ;
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
