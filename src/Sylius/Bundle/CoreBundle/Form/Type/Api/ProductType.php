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
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Taxon form type.
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
            ->add('taxons', 'entity', array(
                'multiple' => true,
                'class' => 'Sylius\Component\Core\Model\Taxon'
            ))
            ->add('price', 'sylius_money', array(
                'property_path' => 'masterVariant.price'
            ))
            ->add('onHand', 'integer', array(
                'property_path' => 'masterVariant.onHand'
            ))
            ->add('sku', 'text', array(
                'property_path' => 'masterVariant.sku'
            ))
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
