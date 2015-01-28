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

use Sylius\Bundle\ProductBundle\Form\Type\ProductType as BaseProductType;
use Sylius\Component\Core\Model\Product;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Product form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductType extends BaseProductType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('translations', 'a2lix_translationsForms', array(
                'form_type' => 'sylius_product_translation',
                'label'    => 'sylius.form.product.translations'
            ))
            ->add('taxCategory', 'sylius_tax_category_choice', array(
                'required'    => false,
                'empty_value' => '---',
                'label'       => 'sylius.form.product.tax_category'
            ))
            ->add('shippingCategory', 'sylius_shipping_category_choice', array(
                'required'    => false,
                'empty_value' => '---',
                'label'       => 'sylius.form.product.shipping_category'
            ))
            ->add('taxons', 'sylius_taxon_selection')
            ->add('variantSelectionMethod', 'choice', array(
                'label'   => 'sylius.form.product.variant_selection_method',
                'choices' => Product::getVariantSelectionMethodLabels()
            ))
            ->add('restrictedZone', 'sylius_zone_choice', array(
                'empty_value' => '---',
                'label'       => 'sylius.form.product.restricted_zone',
            ))
        ;
    }
}
