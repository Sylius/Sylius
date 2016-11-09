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
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelAndCurrencyBasedCalculatorSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext, CurrencyContextInterface $currencyContext)
    {
        $this->beConstructedWith($channelContext, $currencyContext);
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
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        CurrencyContextInterface $currencyContext,
        PriceableInterface $subject
    ) {
        $subject->getPricingConfiguration()->willReturn([
            'WEB-EU' => ['EUR' => 1000, 'GBP' => 500],
            'WEB-GB' => ['EUR' => 10000, 'GBP' => 5000],
        ]);

        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB-EU');

        $currencyContext->getCurrencyCode()->willReturn('EUR');

        $this->calculate($subject, [])->shouldReturn(1000);
    }

    function it_returns_subject_price_if_no_required_configuration_is_set(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        CurrencyContextInterface $currencyContext,
        PriceableInterface $subject
    ) {
        $subject->getPricingConfiguration()->willReturn([
            'WEB-EU' => ['EUR' => 1000, 'GBP' => 500],
            'WEB-GB' => ['GBP' => 10000],
        ]);

        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB-GB');

        $currencyContext->getCurrencyCode()->willReturn('EUR');
        $subject->getPrice()->willReturn(15000);

        $this->calculate($subject, [])->shouldReturn(15000);
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn(Calculators::CHANNEL_AND_CURRENCY_BASED);
    }
}
