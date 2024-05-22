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

use Sylius\Bundle\ProductBundle\Form\Type\ProductGenerateVariantsType as BaseProductGenerateVariantsType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantGenerationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class ProductGenerateVariantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('variants', LiveCollectionType::class, [
                'entry_type' => ProductVariantGenerationType::class,
                'allow_add' => false,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'button_delete_options' => [
                    'label' => false,
                ],
            ]);
    }

    public function getParent(): string
    {
        return BaseProductGenerateVariantsType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_product_generate_variants';
    }
}
