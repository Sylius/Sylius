<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Pricing;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Pricing\Calculators;
use Sylius\Component\Core\Pricing\ChannelAndCurrencyBasedCalculator;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelAndCurrencyBasedCalculatorSpec extends ObjectBehavior
{
    function let(CurrencyConverterInterface $currencyConverter)
    {
        $this->beConstructedWith($currencyConverter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelAndCurrencyBasedCalculator::class);
    }

    function it_implements_calculator_interface()
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_returns_price_for_given_channel_and_currency_from_configuration(
        ChannelInterface $channel,
        CurrencyConverterInterface $currencyConverter,
        PriceableInterface $subject
    ) {
        $subject->getPricingConfiguration()->willReturn([
            'WEB-EU' => ['EUR' => 1000, 'GBP' => 500],
            'WEB-GB' => ['EUR' => 10000, 'GBP' => 5000],
        ]);

        $channel->getCode()->willReturn('WEB-EU');

        $currencyConverter->convertToBase(1000, 'EUR')->willReturn(1250);

        $this->calculate($subject, [], ['currency' => 'EUR', 'channel' => $channel])->shouldReturn(1250);
    }

    function it_returns_subject_price_if_no_required_configuration_is_set(
        ChannelInterface $channel,
        PriceableInterface $subject
    ) {
        $subject->getPricingConfiguration()->willReturn([
            'WEB-EU' => ['EUR' => 1000, 'GBP' => 500],
            'WEB-GB' => ['GBP' => 10000],
        ]);

        $channel->getCode()->willReturn('WEB-GB');

        $subject->getPrice()->willReturn(15000);

        $this->calculate($subject, [], ['currency' => 'EUR', 'channel' => $channel])->shouldReturn(15000);
    }

    function it_throws_exception_if_context_data_is_invalid(
        ChannelInterface $channel,
        PriceableInterface $subject
    ) {
        $this
            ->shouldThrow(new \InvalidArgumentException('You should configure currency and channel to determine a price.'))
            ->during('calculate', [$subject, [], ['channel' => $channel]])
        ;

        $this
            ->shouldThrow(new \InvalidArgumentException('You should configure currency and channel to determine a price.'))
            ->during('calculate', [$subject, [], ['currency' => 'EUR']])
        ;

        $this
            ->shouldThrow(new \InvalidArgumentException('You should configure currency and channel to determine a price.'))
            ->during('calculate', [$subject, [], []])
        ;
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn(Calculators::CHANNEL_AND_CURRENCY_BASED);
    }
}
