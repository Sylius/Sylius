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

use Sylius\Bundle\VariableProductBundle\Form\Type\VariantType as BaseVariantType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Product variant type.
 * We need to add only simple price field.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantType extends BaseVariantType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('sku', 'text', array(
                'label' => 'sylius.form.variant.sku'
            ))
            ->add('price', 'sylius_money', array(
                'label' => 'sylius.form.variant.price'
            ))
            ->add('availableOnDemand', 'checkbox', array(
                'label' => 'sylius.form.variant.available_on_demand'
            ))
            ->add('onHand', 'integer', array(
                'label' => 'sylius.form.variant.on_hand'
            ))
            ->add('images', 'collection', array(
                'type'         => 'sylius_image',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label'        => 'sylius.form.variant.images'
            ))
            ->add('width', 'number', array(
                'required' => false,
                'label'    => 'sylius.form.variant.width'
            ))
            ->add('height', 'number', array(
                'required' => false,
                'label'    => 'sylius.form.variant.height'
            ))
            ->add('depth', 'number', array(
                'required' => false,
                'label'    => 'sylius.form.variant.depth'
            ))
            ->add('weight', 'number', array(
                'required' => false,
                'label'    => 'sylius.form.variant.weight'
            ))
        ;
    }
}
