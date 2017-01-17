<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Calculator;

use Sylius\Component\Shipping\Model\ShipmentInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CalculatorInterface
{
    /**
     * @param ShipmentInterface $subject
     * @param array $configuration
     *
     * @return int
     */
    public function calculate(ShipmentInterface $subject, array $configuration);

    /**
     * @return string
     */
    public function getType();
}
