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
use Sylius\Bundle\CoreBundle\Form\Type\ProductTaxonChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType as BaseProductType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonChoiceType;
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
            ->add('channels', ResourceChoiceType::class, [
                'resource' => 'sylius.channel',
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.product.channels',
            ])
            ->add('mainTaxon', ResourceChoiceType::class, [
                'resource' => 'sylius.taxon',
            ])
            ->add('productTaxons', ProductTaxonChoiceType::class, [
                'label' => 'sylius.form.product.taxons',
                'multiple' => true,
            ])
            ->add('variantSelectionMethod', ChoiceType::class, [
                'label' => 'sylius.form.product.variant_selection_method',
                'choices' => array_flip(Product::getVariantSelectionMethodLabels()),
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ProductImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'sylius.form.product.images',
            ])
            ->addEventSubscriber(new AddProductOnProductTaxonFormSubscriber())
        ;
    }
}
