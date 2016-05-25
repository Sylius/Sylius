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

use Sylius\Bundle\ProductBundle\Form\Type\LegacyProductType as BaseProductType;
use Sylius\Component\Core\Model\Product;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class LegacyProductType extends BaseProductType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('translations', 'sylius_translations', [
                'type' => 'sylius_product_translation',
                'label' => 'sylius.form.product.translations',
            ])
            ->add('shippingCategory', 'sylius_shipping_category_choice', [
                'required' => false,
                'empty_value' => '---',
                'label' => 'sylius.form.product.shipping_category',
            ])
            ->add('taxons', 'sylius_taxon_choice', [
                'label' => 'sylius.form.product.taxons',
                'multiple' => true,
                'attr' => ['style' => 'height: 30%']
            ])
            ->add('variantSelectionMethod', 'choice', [
                'label' => 'sylius.form.product.variant_selection_method',
                'choices' => Product::getVariantSelectionMethodLabels(),
            ])
            ->add('channels', 'sylius_channel_choice', [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.product.channels',
            ])
            ->add('restrictedZone', 'sylius_zone_choice', [
                'empty_value' => '---',
                'label' => 'sylius.form.product.restricted_zone',
            ])
            ->add('mainTaxon', 'sylius_taxon_choice', [
                'label' => 'sylius.form.product.main_taxon',
             ])
        ;
    }
}
