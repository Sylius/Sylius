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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CalculatorRegistryInterface
{
    /**
     * @return CalculatorInterface[]
     */
    public function getCalculators();

    /**
     * @param string              $name
     * @param CalculatorInterface $calculator
     */
    public function registerCalculator($name, CalculatorInterface $calculator);

    /**
     * @param string $name
     */
    public function unregisterCalculator($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasCalculator($name);

    /**
     * @param string $name
     *
     * @return CalculatorInterface
     *
     * @throw NonExistingCalculatorException
     */
    public function getCalculator($name);
}
