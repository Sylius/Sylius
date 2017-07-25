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

namespace spec\Sylius\Component\Core\Currency\Context;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Model\Currency;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class ChannelAwareCurrencyContextSpec extends ObjectBehavior
{
    function let(CurrencyContextInterface $currencyContext, ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($currencyContext, $channelContext);
    }

    function it_is_a_currency_context()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_returns_the_currency_code_from_decorated_context_if_it_is_available_in_current_channel(
        CurrencyContextInterface $currencyContext,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $eur = new Currency();
        $eur->setCode('EUR');

        $usd = new Currency();
        $usd->setCode('USD');

        $channel->getCurrencies()->willReturn(new ArrayCollection([$eur, $usd]));
        $channelContext->getChannel()->willReturn($channel);

        $currencyContext->getCurrencyCode()->willReturn('USD');

        $this->getCurrencyCode()->shouldReturn('USD');
    }

    function it_returns_the_channels_base_currency_if_the_one_from_context_is_not_available(
        CurrencyContextInterface $currencyContext,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $eur = new Currency();
        $eur->setCode('EUR');

        $channel->getBaseCurrency()->willReturn($eur);
        $channel->getCurrencies()->willReturn(new ArrayCollection([$eur]));
        $channelContext->getChannel()->willReturn($channel);

        $currencyContext->getCurrencyCode()->willReturn('USD');

        $this->getCurrencyCode()->shouldReturn('EUR');
    }

    function it_returns_the_channels_base_currency_if_currency_was_not_found(
        CurrencyContextInterface $currencyContext,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $eur = new Currency();
        $eur->setCode('EUR');

        $channel->getBaseCurrency()->willReturn($eur);
        $channelContext->getChannel()->willReturn($channel);

        $currencyContext->getCurrencyCode()->willThrow(CurrencyNotFoundException::class);

        $this->getCurrencyCode()->shouldReturn('EUR');
    }
}
