<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Calculator;

use Sylius\Bundle\TaxationBundle\Model\TaxRateInterface;

/**
 * Delegating calculator.
 * It uses proper calculator to calculate the amount of tax.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DelegatingCalculator implements CalculatorInterface
{
    /**
     * Calculators hash by name.
     *
     * @var array
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
     * Get all calculators.
     *
     * @return CalculatorInterface[]
     */
    public function getCalculators()
    {
        return $this->calculators;
    }

    /**
     * Register calculator under given name.
     *
     * @param string              $name
     * @param CalculatorInterface $calculator
     *
     * @throws \InvalidArgumentException
     */
    public function registerCalculator($name, CalculatorInterface $calculator)
    {
        if ($this->hasCalculator($name)) {
            throw new \InvalidArgumentException(sprintf('Calculator with name "%s" is already registered', $name));
        }

        $this->calculators[$name] = $calculator;
    }

    /**
     * Unregister calculator.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     */
    public function unregisterCalculator($name)
    {
        if (!$this->hasCalculator($name)) {
            throw new \InvalidArgumentException(sprintf('Calculator with name "%s" does not exist', $name));
        }

        unset($this->calculators[$name]);
    }

    /**
     * Has calculator with name registered?
     *
     * @param string $name
     *
     * @return Boolean
     */
    public function hasCalculator($name)
    {
        return isset($this->calculators[$name]);
    }

    /**
     * Get calculator registered under given name.
     *
     * @param string $name
     *
     * @return CalculatorInterface
     *
     * @throws \InvalidArgumentException
     */
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
    public function calculate($base, TaxRateInterface $rate)
    {
        $calculator = $this->getCalculator($rate->getCalculator());

        return $calculator->calculate($base, $rate);
    }
}
