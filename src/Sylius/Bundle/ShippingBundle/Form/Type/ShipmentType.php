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

use Sylius\Bundle\ShippingBundle\Model\ShipmentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Shipping form type.
 */
class ShipmentType extends AbstractType
{
    /**
     * Data class.
     *
     * @var string
     */
    protected $dataClass;

    /**
     * Validation groups.
     *
     * @var array
     */
    protected $validationGroups;

    /**
     * Constructor.
     *
     * @param string $dataClass
     */
    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('state', 'choice', array(
                'label'   => 'sylius.form.shipment.state',
                'choices' => array(
                    ShipmentInterface::STATE_CHECKOUT   => 'sylius.form.shipment.states.checkout',
                    ShipmentInterface::STATE_DISPATCHED => 'sylius.form.shipment.states.dispatched',
                    ShipmentInterface::STATE_PENDING    => 'sylius.form.shipment.states.pending',
                    ShipmentInterface::STATE_READY      => 'sylius.form.shipment.states.ready',
                    ShipmentInterface::STATE_SHIPPED    => 'sylius.form.shipment.states.shipped',
                    ShipmentInterface::STATE_RETURNED   => 'sylius.form.shipment.states.returned',
                ),
            ))
            ->add('tracking', 'text', array(
                'label'    => 'sylius.form.shipment.tracking_code',
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass,
            ))
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
