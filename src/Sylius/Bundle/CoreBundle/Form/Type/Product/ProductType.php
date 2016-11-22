<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Product;

use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddProductOnProductTaxonFormSubscriber;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType as BaseProductType;
use Sylius\Component\Core\Model\Product;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Anna Walasek <anna.walasek@lakion.com>
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
            ->add('channels', 'sylius_channel_choice', [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.product.channels',
            ])
            ->add('mainTaxon', 'sylius_taxon_to_hidden_identifier')
            ->add('productTaxons', 'sylius_product_taxon_choice', [
                'label' => 'sylius.form.product.taxons',
                'multiple' => true,
            ])
            ->add('variantSelectionMethod', ChoiceType::class, [
                'label' => 'sylius.form.product.variant_selection_method',
                'choices' => Product::getVariantSelectionMethodLabels(),
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => 'sylius_product_image',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'sylius.form.product.images',
            ])
            ->addEventSubscriber(new AddProductOnProductTaxonFormSubscriber())
        ;
    }
}
