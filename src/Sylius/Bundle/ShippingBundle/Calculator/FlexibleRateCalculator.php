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
 * Calculator which charges a one rate for first item and other for next items.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class FlexibleRateCalculator extends Calculator
{
    /**
     * Calculates flat rate per item on the shipment.
     *
     * @return mixed
     */
    public function calculate(ShipmentInterface $shipment)
    {
        $configuration = $shipment->getMethod()->getConfiguration();

        $limit = $configuration['limit'];
        $amount = $configuration['amount'];
        $rate = $configuration['rate'];

        $totalItems = $shipment->getItems()->count();
        $additionalItems = $totalItems - 1;

        if (0 !== $limit) {
            $additionalItems = $limit <= $additionalItems ? $additionalItems : $limit;
        }

        return $amount + $additionalItems * $rate;
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
        $builder
            ->add('amount', 'money', array(
                'label' => 'First item cost',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')
                ))
            ))
            ->add('rate', 'money', array(
                'label' => 'Additional items cost',
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')
                ))
            ))
            ->add('limit', 'integer', array(
                'label' => 'Additional items limit',
                'constraints' => array(
                    new Type(array('type' => 'integer')
                ))
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfiguration(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array(
                'amount',
                'rate'
            ))
            ->setDefaults(array(
                'limit' => 0,
            ))
        ;
    }
}
