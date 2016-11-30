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
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Currency\Context\StorageBasedCurrencyContext;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class StorageBasedCurrencyContextSpec extends ObjectBehavior
{
    function let(
        ChannelContextInterface $channelContext,
        CurrencyStorageInterface $currencyStorage,
        CurrencyProviderInterface $currencyProvider
    ) {
        $this->beConstructedWith($channelContext, $currencyStorage, $currencyProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(StorageBasedCurrencyContext::class);
    }

    function it_is_a_currency_context()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_returns_an_available_active_currency(
        ChannelContextInterface $channelContext,
        CurrencyStorageInterface $currencyStorage,
        CurrencyProviderInterface $currencyProvider,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $currencyStorage->get($channel)->willReturn('BTC');

        $currencyProvider->getAvailableCurrenciesCodes()->willReturn(['BTC', 'LTC']);

        $this->getCurrencyCode()->shouldReturn('BTC');
    }

    function it_throws_an_exception_if_currency_taken_from_storage_is_not_available(
        ChannelContextInterface $channelContext,
        CurrencyStorageInterface $currencyStorage,
        CurrencyProviderInterface $currencyProvider,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $currencyStorage->get($channel)->willReturn('BTC');

        $currencyProvider->getAvailableCurrenciesCodes()->willReturn(['LTC', 'PLN']);

        $this->shouldThrow(CurrencyNotFoundException::class)->during('getCurrencyCode');
    }
}
