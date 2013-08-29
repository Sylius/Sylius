<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Calculator\Registry;

use Sylius\Bundle\ShippingBundle\Calculator\CalculatorInterface;

/**
 * Calculator registry.
 *
 * This service keeps all calculators registered inside
 * container. Allows to retrieve them by name.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CalculatorRegistry implements CalculatorRegistryInterface
{
    /**
     * Calculators array.
     *
     * @var CalculatorInterface[]
     */
    protected $calculators;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->calculators = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculators()
    {
        return $this->calculators;
    }

    /**
     * {@inheritdoc}
     */
    public function registerCalculator($name, CalculatorInterface $calculator)
    {
        if ($this->hasCalculator($name)) {
            throw new ExistingCalculatorException($name);
        }

        $this->calculators[$name] = $calculator;
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterCalculator($name)
    {
        if (!$this->hasCalculator($name)) {
            throw new NonExistingCalculatorException($name);
        }

        unset($this->calculators[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasCalculator($name)
    {
        return isset($this->calculators[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculator($name)
    {
        if (!$this->hasCalculator($name)) {
            throw new NonExistingCalculatorException($name);
        }

        return $this->calculators[$name];
    }
}
