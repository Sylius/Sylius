<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Calculator;

use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Per weight amount rate calculator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class WeightRateCalculator extends Calculator
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ShippingSubjectInterface $subject, array $configuration)
    {
        return $configuration['amount'] * ($subject->getShippingWeight() / $configuration['division']);
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
    public function getConfigurationFormType()
    {
        return 'sylius_shipping_calculator_weight_rate_configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'division' => 1
            ))
            ->setRequired(array(
                'amount',
                'division'
            ))
            ->setAllowedTypes(array(
                'amount'   => array('numeric'),
                'division' => array('numeric')
            ))
        ;
    }
}
