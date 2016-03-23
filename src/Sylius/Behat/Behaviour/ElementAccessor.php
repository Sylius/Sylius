<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Behaviour;

use Behat\Mink\Element\NodeElement;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
trait ElementAccessor
{
    /**
     * @param string $name
     * 
     * @return NodeElement
     */
    abstract public function getElement($name);
}
