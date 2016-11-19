<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Provider\ChannelBasedCurrencyProvider;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelBasedCurrencyProviderSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($channelContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelBasedCurrencyProvider::class);
    }

    function it_is_a_currency_provider()
    {
        $this->shouldImplement(CurrencyProviderInterface::class);
    }

    function it_returns_only_channels_enabled_currencies_as_available_ones(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        CurrencyInterface $enabledCurrency,
        CurrencyInterface $disabledCurrency
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $channel->getCurrencies()->willReturn(new ArrayCollection([
            $enabledCurrency->getWrappedObject(),
            $disabledCurrency->getWrappedObject(),
        ]));

        $enabledCurrency->isEnabled()->willReturn(true);
        $disabledCurrency->isEnabled()->willReturn(false);

        $enabledCurrency->getCode()->willReturn('BTC');

        $this->getAvailableCurrenciesCodes()->shouldReturn(['BTC']);
    }

    function it_returns_channels_base_currency(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        CurrencyInterface $currency
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $channel->getBaseCurrency()->willReturn($currency);

        $currency->getCode()->willReturn('BTC');

        $this->getDefaultCurrencyCode()->shouldReturn('BTC');
    }

    function it_throws_a_currency_not_found_exception_if_channel_cannot_be_determined(
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->shouldThrow(CurrencyNotFoundException::class)->during('getAvailableCurrenciesCodes');
        $this->shouldThrow(CurrencyNotFoundException::class)->during('getDefaultCurrencyCode');
    }
}
