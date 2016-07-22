<?php

namespace spec\Sylius\Component\Core\Currency;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Currency\CurrencyStorage;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Storage\StorageInterface;

/**
 * @mixin CurrencyStorage
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CurrencyStorageSpec extends ObjectBehavior
{
    function let(StorageInterface $storage)
    {
        $this->beConstructedWith($storage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Currency\CurrencyStorage');
    }

    function it_is_a_currency_storage()
    {
        $this->shouldImplement(CurrencyStorageInterface::class);
    }

    function it_sets_currency_for_given_channel(
        StorageInterface $storage,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('web');

        $storage->setData('_currency_web', 'BTC')->shouldBeCalled();

        $this->set($channel, 'BTC');
    }

    function it_gets_currency_for_given_channel(
        StorageInterface $storage,
        ChannelInterface $channel,
        CurrencyInterface $currency
    ) {
        $channel->getCode()->willReturn('web');

        $storage->getData('_currency_web')->willReturn('BTC');

        $this->get($channel)->shouldReturn('BTC');
    }

    function it_throws_a_currency_not_found_exception_if_storage_does_not_have_currency_code_for_given_channel(
        StorageInterface $storage,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('web');

        $storage->getData('_currency_web')->willReturn(null);

        $this->shouldThrow(CurrencyNotFoundException::class)->during('get', [$channel]);
    }
}
