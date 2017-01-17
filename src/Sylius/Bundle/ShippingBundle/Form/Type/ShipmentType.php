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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ShipmentType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('state', ChoiceType::class, [
                'choices' => [
                    'sylius.form.shipment.states.cart' => ShipmentInterface::STATE_CART,
                    'sylius.form.shipment.states.ready' => ShipmentInterface::STATE_READY,
                    'sylius.form.shipment.states.shipped' => ShipmentInterface::STATE_SHIPPED,
                    'sylius.form.shipment.states.cancelled' => ShipmentInterface::STATE_CANCELLED,
                ],
                'label' => 'sylius.form.shipment.state',
            ])
            ->add('tracking', TextType::class, [
                'label' => 'sylius.form.shipment.tracking_code',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_shipment';
    }
}
