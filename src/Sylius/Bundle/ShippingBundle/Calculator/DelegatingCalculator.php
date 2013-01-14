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

use Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorregistryInterface;
use Sylius\Bundle\ShippingBundle\Model\ShipmentInterface;

/**
 * This class delegates the calculation of charge for particular shipment
 * to a correct calculator instance, based on the name defined in shipping method.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DelegatingCalculator extends Calculator
{
    /**
     * Calculator registry.
     *
     * @var CalculatorRegistryInterface
     */
    protected $registry;

    /**
     * Constructor.
     *
     * @param CalculatorRegistryInterface $registry
     */
    public function __construct(CalculatorRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(ShipmentInterface $shipment)
    {
        if (null === $method = $shipment->getMethod()) {
            throw new UndefinedShippingMethodException('Cannot calculate charge on shipment without defined shipping method');
        }

        $calculator = $this->registry->getCalculator($method->getCalculator());

        return $calculator->calculate($shipment);
    }
}
