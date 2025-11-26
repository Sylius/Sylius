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

use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionType as BaseProductOptionType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class ProductOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('values', LiveCollectionType::class, [
            'entry_type' => ProductOptionValueType::class,
            'label' => false,
            'button_add_options' => [
                'label' => 'sylius.form.option_value.add_value',
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'delete_empty' => true,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_product_option';
    }

    public function getParent(): string
    {
        return BaseProductOptionType::class;
    }
}
