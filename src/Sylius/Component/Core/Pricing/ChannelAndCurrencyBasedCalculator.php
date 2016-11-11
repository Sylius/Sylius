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
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ChannelAndCurrencyBasedCalculator implements CalculatorInterface
{
    /**
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    /**
     * @param CurrencyConverterInterface $currencyConverter
     */
    public function __construct(CurrencyConverterInterface $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = [])
    {
        if (!isset($context['channel']) || !isset($context['currency'])) {
            throw new \InvalidArgumentException('You should configure currency and channel to determine a price.');
        }

        $pricingConfiguration = $subject->getPricingConfiguration();

        $channel = $context['channel'];
        $currencyCode = $context['currency'];

        if (!isset($pricingConfiguration[$channel->getCode()][$currencyCode])) {
            return $subject->getPrice();
        }

        return $this->currencyConverter->convertToBase(
            $pricingConfiguration[$channel->getCode()][$currencyCode],
            $currencyCode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::CHANNEL_AND_CURRENCY_BASED;
    }
}
