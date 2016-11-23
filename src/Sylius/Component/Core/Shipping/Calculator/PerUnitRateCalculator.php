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

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class PerUnitRateCalculator implements CalculatorInterface
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param CalculatorInterface $calculator
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(CalculatorInterface $calculator, ChannelContextInterface $channelContext)
    {
        $this->calculator = $calculator;
        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(ShipmentInterface $subject, array $configuration)
    {
        $channel = $this->channelContext->getChannel();

        return (int) ($configuration[$channel->getCode()]['amount'] * $subject->getShippingUnitCount());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->calculator->getType();
    }
}
