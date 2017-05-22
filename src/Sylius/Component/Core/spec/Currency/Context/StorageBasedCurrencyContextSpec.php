<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Currency\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class StorageBasedCurrencyContextSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext, CurrencyStorageInterface $currencyStorage)
    {
        $this->beConstructedWith($channelContext, $currencyStorage);
    }

    function it_is_a_currency_context()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_returns_an_available_active_currency(
        ChannelContextInterface $channelContext,
        CurrencyStorageInterface $currencyStorage,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $currencyStorage->get($channel)->willReturn('BTC');

        $this->getCurrencyCode()->shouldReturn('BTC');
    }

    function it_throws_an_exception_if_storage_does_not_have_currency_code(
        ChannelContextInterface $channelContext,
        CurrencyStorageInterface $currencyStorage,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $currencyStorage->get($channel)->willReturn(null);

        $this->shouldThrow(CurrencyNotFoundException::class)->during('getCurrencyCode');
    }
}
