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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Calculator which charges a flat rate per item.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PerItemRateCalculator extends Calculator
{
    /**
     * Calculates flat rate per item on the shipment.
     *
     * @return mixed
     */
    public function calculate(ShipmentInterface $shipment)
    {
        $configuration = $shipment->getMethod()->getConfiguration();

        return $configuration['amount'] * $shipment->getItems()->count();
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
        return 'sylius_shipping_calculator_per_item_rate_configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array(
                'amount'
            ))
            ->setAllowedTypes(array(
                'amount' => array('numeric')
            ))
        ;
    }
}
