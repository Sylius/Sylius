<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Shipping\Calculator;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface  as BaseShipmentInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class FlatRateCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(BaseShipmentInterface $subject, array $configuration)
    {
        Assert::isInstanceOf($subject, ShipmentInterface::class);

        $channelCode = $subject->getOrder()->getChannel()->getCode();

        return (int) $configuration[$channelCode]['amount'];
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'flat_rate';
    }
}
