<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Pricing;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelAndCurrencyBasedCalculator implements CalculatorInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @param ChannelContextInterface $channelContext
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(ChannelContextInterface $channelContext, CurrencyContextInterface $currencyContext)
    {
        $this->channelContext = $channelContext;
        $this->currencyContext = $currencyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = [])
    {
        $pricingConfiguration = $subject->getPricingConfiguration();

        $channel = $this->channelContext->getChannel();
        $currencyCode = $this->currencyContext->getCurrencyCode();

        if (!isset($pricingConfiguration[$channel->getCode()][$currencyCode])) {
            return $subject->getPrice();
        }

        return $pricingConfiguration[$channel->getCode()][$currencyCode];
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::CHANNEL_AND_CURRENCY_BASED;
    }
}
