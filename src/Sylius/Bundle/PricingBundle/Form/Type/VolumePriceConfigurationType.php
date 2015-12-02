<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PricingBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Volume based pricing configuration form type.
 *
 * @author Liverbool <nukboon@gmail.com>
 */
class VolumePriceConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('min', NumberType::class, array(
                'label' => 'sylius.form.pricing.volume_based.min'
            ))
            ->add('max', NumberType::class, array(
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
    public function getBlockPrefix()
    {
        return 'sylius_price_calculator_volume_based_configuration';
    }
}
