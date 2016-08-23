<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Storage;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Storage\ContextBasedCurrencyStorage;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;

/**
 * @mixin ContextBasedCurrencyStorage
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ContextBasedCurrencyStorageSpec extends ObjectBehavior
{
    function let(CurrencyStorageInterface $currencyStorage, ShopperContextInterface $shopperContext)
    {
        $this->beConstructedWith($currencyStorage, $shopperContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContextBasedCurrencyStorage::class);
    }

    function it_implements_currency_storage_interface()
    {
        $this->shouldImplement(CurrencyStorageInterface::class);
    }

    function it_throws_currency_not_found_exception_on_get_with_no_given_channel_when_the_currency_is_not_present_in_the_storage_nor_in_the_context(
        CurrencyStorageInterface $currencyStorage,
        ShopperContextInterface $shopperContext,
        ChannelInterface $channel
    ) {
        $shopperContext->getChannel()->willReturn($channel);
        $currencyStorage->get($channel)->willThrow(CurrencyNotFoundException::class);
        $shopperContext->getCurrencyCode()->willThrow(CurrencyNotFoundException::class);

        $this->shouldThrow(CurrencyNotFoundException::class)->during('get', []);
    }

    function it_throws_currency_not_found_exception_on_get_when_a_currency_for_given_channel_was_not_set_in_storage_and_there_is_no_currency_for_current_channel_in_context(
        CurrencyStorageInterface $currencyStorage,
        ShopperContextInterface $shopperContext,
        ChannelInterface $channel
    ) {
        $shopperContext->getChannel()->shouldNotBeCalled();
        $currencyStorage->get($channel)->willThrow(CurrencyNotFoundException::class);
        $shopperContext->getCurrencyCode()->willThrow(CurrencyNotFoundException::class);

        $this->shouldThrow(CurrencyNotFoundException::class)->during('get', [$channel]);
    }

    function it_gets_the_currency_from_the_storage_when_a_channel_is_given_and_present_in_the_storage(
        CurrencyStorageInterface $currencyStorage,
        ShopperContextInterface $shopperContext,
        ChannelInterface $channel
    ) {
        $shopperContext->getChannel()->shouldNotBeCalled();
        $currencyStorage->get($channel)->willReturn('EUR');

        $this->get($channel)->shouldReturn('EUR');
    }

    function it_gets_the_currency_from_the_current_channel_from_storage_when_a_channel_was_not_given(
        CurrencyStorageInterface $currencyStorage,
        ShopperContextInterface $shopperContext,
        ChannelInterface $channel
    ) {
        $shopperContext->getChannel()->willReturn($channel);
        $currencyStorage->get($channel)->willReturn('EUR');

        $this->get()->shouldReturn('EUR');
    }

    function it_gets_the_currency_from_the_current_channel_from_context_when_a_channel_was_given_but_is_not_present_in_the_storage(
        CurrencyStorageInterface $currencyStorage,
        ShopperContextInterface $shopperContext,
        ChannelInterface $channel
    ) {
        $shopperContext->getChannel()->shouldNotBeCalled();
        $currencyStorage->get($channel)->willThrow(CurrencyNotFoundException::class);
        $shopperContext->getCurrencyCode()->willReturn('EUR');

        $this->get($channel)->shouldReturn('EUR');
    }

    function it_sets_the_currency_on_given_channel(
        CurrencyStorageInterface $currencyStorage,
        ShopperContextInterface $shopperContext,
        ChannelInterface $channel
    ) {
        $shopperContext->getChannel()->shouldNotBeCalled();
        $currencyStorage->set($channel, 'EUR')->shouldBeCalled();

        $this->set($channel, 'EUR');
    }

    function it_sets_the_currency_on_the_current_channel_when_no_channel_was_given(
        CurrencyStorageInterface $currencyStorage,
        ShopperContextInterface $shopperContext,
        ChannelInterface $channel
    ) {
        $shopperContext->getChannel()->willReturn($channel);
        $currencyStorage->set($channel, 'EUR')->shouldBeCalled();

        $this->set(null, 'EUR');
    }
}
