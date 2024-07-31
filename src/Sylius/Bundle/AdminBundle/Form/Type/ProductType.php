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

use Sylius\Bundle\ProductBundle\Form\Type\ProductType as BaseProductType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class ProductType extends AbstractType
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('images', LiveCollectionType::class, [
                'entry_type' => ProductImageType::class,
                'entry_options' => ['product' => $options['data']],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'sylius.form.product.images',
                'block_name' => 'entry',
            ])
            ->add('associations', ProductAssociationsType::class, [
                'label' => false,
            ])
            ->add('mainTaxon', TaxonAutocompleteType::class, [
                'label' => 'sylius.form.product.main_taxon',
                'multiple' => false,
            ])
        ;
    }

    public function getParent(): string
    {
        return BaseProductType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_product';
    }
}
