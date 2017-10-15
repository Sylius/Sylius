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

namespace spec\Sylius\Component\Core\Locale;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Storage\StorageInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class LocaleStorageSpec extends ObjectBehavior
{
    function let(StorageInterface $storage): void
    {
        $this->beConstructedWith($storage);
    }

    function it_is_a_locale_storage(): void
    {
        $this->shouldImplement(LocaleStorageInterface::class);
    }

    function it_sets_a_locale_for_a_given_channel(StorageInterface $storage, ChannelInterface $channel): void
    {
        $channel->getCode()->willReturn('web');

        $storage->set('_locale_web', 'BTC')->shouldBeCalled();

        $this->set($channel, 'BTC');
    }

    function it_gets_a_locale_for_a_given_channel(StorageInterface $storage, ChannelInterface $channel): void
    {
        $channel->getCode()->willReturn('web');

        $storage->get('_locale_web')->willReturn('BTC');

        $this->get($channel)->shouldReturn('BTC');
    }

    function it_throws_a_locale_not_found_exception_if_storage_does_not_have_locale_code_for_given_channel(
        StorageInterface $storage,
        ChannelInterface $channel
    ): void {
        $channel->getCode()->willReturn('web');

        $storage->get('_locale_web')->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('get', [$channel]);
    }
}
