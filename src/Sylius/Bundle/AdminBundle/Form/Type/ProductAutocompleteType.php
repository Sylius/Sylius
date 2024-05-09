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

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField(
    alias: 'sylius_admin_product',
    route: 'sylius_admin_entity_autocomplete_admin',
)]
final class ProductAutocompleteType extends AbstractType
{
    public function __construct(
        private readonly string $productClass,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->productClass,
            'choice_name' => 'name',
            'choice_value' => 'code',
            'searchable_fields' => ['code', 'translations.name'],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_product_autocomplete';
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
