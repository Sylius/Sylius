<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Shipment filter type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShipmentFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.shipment_filter.number',
                'attr'     => array(
                    'placeholder' => 'sylius.form.shipment_filter.number'
                )
            ))
            ->add('shippingAddress', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.shipment_filter.shipping_address',
                'attr'     => array(
                    'placeholder' => 'sylius.form.shipment_filter.shipping_address'
                )
            ))
            ->add('createdAtFrom', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.shipment_filter.created_at_from',
                'attr'     => array(
                    'placeholder' => 'sylius.form.shipment_filter.created_at_from'
                )
            ))
            ->add('createdAtTo', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.shipment_filter.created_at_to',
                'attr'     => array(
                    'placeholder' => 'sylius.form.shipment_filter.created_at_to'
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shipment_filter';
    }
}
