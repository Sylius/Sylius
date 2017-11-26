<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Shipping\Calculator;

use Sylius\Component\Shipping\Model\ShipmentInterface;

interface CalculatorInterface
{
    /**
     * @param array $configuration
     *
     */
    public function calculate(ShipmentInterface $subject, array $configuration): int;

    /**
     * @return string
     */
    public function getType(): string;
}
