<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Api;

use Sylius\Bundle\CoreBundle\Form\Type\ProductType as BaseProductType;
use Sylius\Component\Core\Model\Taxon;
use Symfony\Component\Form\FormBuilderInterface;

class ProductType extends BaseProductType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('taxons', 'entity', [
                'multiple' => true,
                'class' => Taxon::class,
            ])
            ->add('price', 'sylius_money', [
                'property_path' => 'masterVariant.price',
            ])
            ->add('onHand', 'integer', [
                'property_path' => 'masterVariant.onHand',
            ])
            ->add('sku', 'text', [
                'property_path' => 'masterVariant.sku',
            ])
            ->add('images', 'collection', [
                'type' => 'sylius_image',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'sylius.form.variant.images',
                'property_path' => 'masterVariant.images',
            ])
            ->remove('masterVariant')
            ->remove('variantSelectionMethod')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_api_product';
    }
}
