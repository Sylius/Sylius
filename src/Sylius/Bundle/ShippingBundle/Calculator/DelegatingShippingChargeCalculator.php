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

use Sylius\Bundle\ShippingBundle\Model\ShippableInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;

/**
 * Delegating calculator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class DelegatingShippingChargeCalculator implements ShippingChargeCalculatorInterface
{
    protected $calculators;

    public function __construct()
    {
        $this->calculators = array();
    }

    public function getCalculators()
    {
        return $this->calculators;
    }

    public function registerCalculator($name, ShippingChargeCalculatorInterface $calculator)
    {
        if ($this->hasCalculator($name)) {
            throw new \InvalidArgumentException(sprintf('Calculator with name "%s" is already registered', $name));
        }

        $this->calculators[$name] = $calculator;
    }

    public function unregisterCalculator($name)
    {
        if (!$this->hasCalculator($name)) {
            throw new \InvalidArgumentException(sprintf('Calculator with name "%s" does not exist', $name));
        }

        unset($this->calculators[$name]);
    }

    public function hasCalculator($name)
    {
        return isset($this->calculators[$name]);
    }

    public function getCalculator($name)
    {
        if (!$this->hasCalculator($name)) {
            throw new \InvalidArgumentException(sprintf('Calculator with name "%s" does not exist', $name));
        }

        return $this->calculators[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(ShippingMethodInterface $method, ShippableInterface $shippable, array $context = array())
    {
        $calculator = $this->getCalculator($method->getCalculator());

        return $calculator->calculate($method, $shippable, $context);
    }
}
