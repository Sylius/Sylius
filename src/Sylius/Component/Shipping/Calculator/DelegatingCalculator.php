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

use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class DelegatingCalculator implements DelegatingCalculatorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $registry;

    /**
     * @param ServiceRegistryInterface $registry
     */
    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(ShipmentInterface $subject): int
    {
        if (null === $method = $subject->getMethod()) {
            throw new UndefinedShippingMethodException('Cannot calculate charge for shipment without a defined shipping method.');
        }

        /** @var CalculatorInterface $calculator */
        $calculator = $this->registry->get($method->getCalculator());

        return $calculator->calculate($subject, $method->getConfiguration());
    }
}
