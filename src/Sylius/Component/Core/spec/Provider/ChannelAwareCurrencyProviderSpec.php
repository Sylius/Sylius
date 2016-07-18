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
use Sylius\Component\Core\Provider\ChannelAwareCurrencyProvider;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;

/**
 * @mixin ChannelAwareCurrencyProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelAwareCurrencyProviderSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($channelContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Provider\ChannelAwareCurrencyProvider');
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

        $this->getAvailableCurrencies()->shouldReturn([$enabledCurrency]);
    }

    function it_returns_channels_default_currency(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        CurrencyInterface $currency
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $channel->getDefaultCurrency()->willReturn($currency);

        $this->getDefaultCurrency()->shouldReturn($currency);
    }

    function it_throws_an_currency_not_found_exception_if_channel_cannot_be_determined(
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->shouldThrow(CurrencyNotFoundException::class)->during('getAvailableCurrencies');
        $this->shouldThrow(CurrencyNotFoundException::class)->during('getDefaultCurrency');
    }
}
