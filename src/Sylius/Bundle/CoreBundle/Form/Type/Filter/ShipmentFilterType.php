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
            ->add('number', 'text', [
                'required' => false,
                'label' => 'sylius.form.shipment_filter.number',
                'attr' => [
                    'placeholder' => 'sylius.form.shipment_filter.number',
                ],
            ])
            ->add('shippingAddress', 'text', [
                'required' => false,
                'label' => 'sylius.form.shipment_filter.shipping_address',
                'attr' => [
                    'placeholder' => 'sylius.form.shipment_filter.shipping_address',
                ],
            ])
            ->add('createdAtFrom', 'text', [
                'required' => false,
                'label' => 'sylius.form.shipment_filter.created_at_from',
                'attr' => [
                    'placeholder' => 'sylius.form.shipment_filter.created_at_from',
                ],
            ])
            ->add('createdAtTo', 'text', [
                'required' => false,
                'label' => 'sylius.form.shipment_filter.created_at_to',
                'attr' => [
                    'placeholder' => 'sylius.form.shipment_filter.created_at_to',
                ],
            ])
            ->add('channel', 'sylius_channel_choice', [
                'required' => false,
                'empty_value' => 'sylius.form.shipment_filter.channel',
            ])
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
