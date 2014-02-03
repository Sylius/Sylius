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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PropertyCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'required'         => false,
                'type'             => 'sylius_product_property',
                'allow_add'        => true,
                'allow_delete'     => true,
                'by_reference'     => false,
                'button_add_label' => 'sylius.product.add_property',
                'item_by_line'     => 2,
            ))
        ;
    }

    public function getParent()
    {
        return 'collection';
    }

    public function getName()
    {
        return 'sylius_property_collection';
    }
}
