<?php

namespace Sylius\Bundle\PricingBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class VolumePriceConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('min', 'number', array(
                'label' => 'sylius.form.pricing.volume_based.min'
            ))
            ->add('max', 'number', array(
                'label' => 'sylius.form.pricing.volume_based.max'
            ))
            ->add('price', 'sylius_money', array(
                'label' => 'sylius.form.pricing.volume_based.price'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_pricing_volume_config';
    }
}
