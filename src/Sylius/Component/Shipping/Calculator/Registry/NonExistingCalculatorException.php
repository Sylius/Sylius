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

/**
 * This exception should be thrown by calculator registry
 * when calculator with given name does not exist.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class NonExistingCalculatorException extends \InvalidArgumentException
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct(sprintf('Shipping cost calculator with name "%s" does not exist', $name));
    }
}
