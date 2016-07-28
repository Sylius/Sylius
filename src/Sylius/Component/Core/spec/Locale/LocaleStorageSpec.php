<?php

namespace spec\Sylius\Component\Core\Locale;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Locale\LocaleStorage;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Storage\StorageInterface;

/**
 * @mixin LocaleStorage
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class LocaleStorageSpec extends ObjectBehavior
{
    function let(StorageInterface $storage)
    {
        $this->beConstructedWith($storage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LocaleStorage::class);
    }

    function it_is_a_locale_storage()
    {
        $this->shouldImplement(LocaleStorageInterface::class);
    }

    function it_sets_locale_for_given_channel(
        StorageInterface $storage,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('web');

        $storage->setData('_locale_web', 'BTC')->shouldBeCalled();

        $this->set($channel, 'BTC');
    }

    function it_gets_locale_for_given_channel(
        StorageInterface $storage,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('web');

        $storage->getData('_locale_web')->willReturn('BTC');

        $this->get($channel)->shouldReturn('BTC');
    }

    function it_throws_a_locale_not_found_exception_if_storage_does_not_have_locale_code_for_given_channel(
        StorageInterface $storage,
        ChannelInterface $channel
    ) {
        $channel->getCode()->willReturn('web');

        $storage->getData('_locale_web')->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('get', [$channel]);
    }
}
