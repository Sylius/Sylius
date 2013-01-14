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
 * Shipping charge calculator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CalculatorInterface
{
    /**
     * Get the shipping cost for given shipment.
     * Shipment has to have at least 1 shippable and a method defined.
     *
     * @param ShipmentInterface $shipment
     *
     * @return mixed
     */
    public function calculate(ShipmentInterface $shipment);

    /**
     * Does this calculator has any configuration?
     *
     * @return Boolean
     */
    public function isConfigurable();

    /**
     * Get calculator configuration form type, if any required.
     *
     * @return string
     */
    public function getConfigurationFormType();

    /**
     * Define the configuration.
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setConfiguration(OptionsResolverInterface $resolver);
}
