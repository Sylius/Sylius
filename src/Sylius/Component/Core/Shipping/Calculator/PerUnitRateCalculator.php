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

namespace Sylius\Component\Core\Shipping\Calculator;

use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;
use Webmozart\Assert\Assert;

final class PerUnitRateCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws MissingChannelConfigurationException
     */
    public function calculate(BaseShipmentInterface $subject, array $configuration): int
    {
        /** @var ShipmentInterface $subject */
        Assert::isInstanceOf($subject, ShipmentInterface::class);

        $channelCode = $subject->getOrder()->getChannel()->getCode();

        if (!isset($configuration[$channelCode])) {
            throw new MissingChannelConfigurationException(sprintf(
                'Channel %s has no amount defined for shipping method %s',
                $subject->getOrder()->getChannel()->getName(),
                $subject->getMethod()->getName()
            ));
        }

        return (int) ($configuration[$channelCode]['amount'] * $subject->getShippingUnitCount());
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'per_unit_rate';
    }
}
