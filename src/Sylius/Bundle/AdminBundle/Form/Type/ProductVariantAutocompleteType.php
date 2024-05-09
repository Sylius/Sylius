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

#[AsEntityAutocompleteField(route: 'sylius_admin_entity_autocomplete_admin')]
final class ProductVariantAutocompleteType extends AbstractType
{
    public function __construct(
        private readonly string $productVariantClass,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->productVariantClass,
            'choice_name' => 'descriptor',
            'choice_value' => 'code',
            'searchable_fields' => ['code'],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_product_variant_autocomplete';
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
