<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Calculator;

use Sylius\Bundle\ShippingBundle\Model\ShipmentInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Calculator which charges a flat rate per shipment.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class FlatRateCalculator extends Calculator
{
    /**
     * Calculates flat rate per item on the shipment.
     *
     * @return mixed
     */
    public function calculate(ShipmentInterface $shipment)
    {
        $configuration = $shipment->getMethod()->getConfiguration();

        return $configuration['amount'];
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigurable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfigurationForm(FormBuilderInterface $builder)
    {
        $builder->add('amount', 'money', array(
            'constraints' => array(
                new NotBlank(),
                new Type(array('type' => 'numeric'))
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfiguration(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array(
                'amount'
            ))
        ;
    }
}
