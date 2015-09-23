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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ExistingCalculatorException extends \InvalidArgumentException
{
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct(sprintf('Shipping cost calculator with name "%s" already exist', $name));
    }
}
