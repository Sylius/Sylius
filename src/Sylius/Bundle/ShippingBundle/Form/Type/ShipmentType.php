<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Shipping form type.
 */
class ShipmentType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('state', 'choice', [
                'label' => 'sylius.form.shipment.state',
                'choices' => [
                    ShipmentInterface::STATE_CHECKOUT => 'sylius.form.shipment.states.checkout',
                    ShipmentInterface::STATE_PENDING => 'sylius.form.shipment.states.pending',
                    ShipmentInterface::STATE_READY => 'sylius.form.shipment.states.ready',
                    ShipmentInterface::STATE_SHIPPED => 'sylius.form.shipment.states.shipped',
                    ShipmentInterface::STATE_RETURNED => 'sylius.form.shipment.states.returned',
                    ShipmentInterface::STATE_CANCELLED => 'sylius.form.shipment.states.cancelled',
                ],
            ])
            ->add('tracking', 'text', [
                'label' => 'sylius.form.shipment.tracking_code',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shipment';
    }
}
