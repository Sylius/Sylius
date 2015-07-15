<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Calculator\Registry;

use Sylius\Component\Shipping\Calculator\CalculatorInterface;

/**
 * Interface for calculator registry.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CalculatorRegistryInterface
{
    /**
     * Get hash of all calculators and their names.
     *
     * @return CalculatorInterface[]
     */
    public function getCalculators();

    /**
     * Register calculator under given name.
     *
     * @param string              $name
     * @param CalculatorInterface $calculator
     */
    public function registerCalculator($name, CalculatorInterface $calculator);

    /**
     * Unregister calculator.
     *
     * @param string $name
     */
    public function unregisterCalculator($name);

    /**
     * Has calculator registered with given name?
     *
     * @param string $name
     *
     * @return Boolean
     */
    public function hasCalculator($name);

    /**
     * Return calculator with given name.
     *
     * @param string $name
     *
     * @return CalculatorInterface
     *
     * @throw NonExistingCalculatorException
     */
    public function getCalculator($name);
}
