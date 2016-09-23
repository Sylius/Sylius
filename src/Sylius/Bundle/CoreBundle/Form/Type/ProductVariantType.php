<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType as BaseProductVariantType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariantType extends BaseProductVariantType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('price', 'sylius_money', [
                'label' => 'sylius.form.variant.price',
            ])
            ->add('originalPrice', 'sylius_money', [
                'required' => false,
                'label' => 'sylius.form.variant.original_price',
            ])
            ->add('tracked', 'checkbox', [
                'label' => 'sylius.form.variant.tracked',
            ])
            ->add('onHand', 'integer', [
                'label' => 'sylius.form.variant.on_hand',
            ])
            ->add('width', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.width',
            ])
            ->add('height', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.height',
            ])
            ->add('depth', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.depth',
            ])
            ->add('weight', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.weight',
            ])
            ->add('taxCategory', 'sylius_tax_category_choice', [
                'required' => false,
                'empty_value' => '---',
                'label' => 'sylius.form.product_variant.tax_category',
            ])
        ;
    }
}
