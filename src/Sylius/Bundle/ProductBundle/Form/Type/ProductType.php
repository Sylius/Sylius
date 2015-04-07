<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Product form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('masterVariant', 'sylius_product_variant', array(
                'master' => true,
            ))
            ->add('attributes', 'collection', array(
                'required'     => false,
                'type'         => 'sylius_product_attribute_value',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
            ->add('associations', 'collection', array(
                'required'     => false,
                'type'         => 'sylius_product_association',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
            ->add('options', 'sylius_product_option_choice', array(
                'required' => false,
                'multiple' => true,
                'label'    => 'sylius.form.product.options'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product';
    }
}
