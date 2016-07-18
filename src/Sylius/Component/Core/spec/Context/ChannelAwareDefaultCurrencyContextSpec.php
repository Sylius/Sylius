<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Context\ChannelAwareDefaultCurrencyContext;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Model\CurrencyInterface;

/**
 * @mixin ChannelAwareDefaultCurrencyContext
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelAwareDefaultCurrencyContextSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($channelContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Context\ChannelAwareDefaultCurrencyContext');
    }

    function it_is_a_currency_context()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_returns_the_channels_default_currency(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        CurrencyInterface $currency
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $channel->getDefaultCurrency()->willReturn($currency);

        $this->getCurrency()->shouldReturn($currency);
    }

    function it_throws_a_currency_not_found_exception_if_channel_cannot_be_determined(
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->shouldThrow(CurrencyNotFoundException::class)->during('getCurrency');
    }
}
