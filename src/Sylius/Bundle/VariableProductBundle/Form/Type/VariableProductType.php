<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Form\Type;

use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Sylius\Bundle\VariableProductBundle\Form\EventListener\BuildProductFormListener;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Variable product form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariableProductType extends ProductType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('availableOn')
            ->add('masterVariant', 'sylius_variant', array(
                'master' => true,
            ))
            ->add('properties', 'collection', array(
                'required'     => false,
                'type'         => 'sylius_product_property',
                'allow_add'    => true,
                'by_reference' => false
            ))
            ->addEventSubscriber(new BuildProductFormListener($builder->getFormFactory()))
        ;
    }
}
