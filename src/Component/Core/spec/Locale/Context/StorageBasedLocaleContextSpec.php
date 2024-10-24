<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Locale\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Resource\Exception\StorageUnavailableException;

final class StorageBasedLocaleContextSpec extends ObjectBehavior
{
    function let(
        ChannelContextInterface $channelContext,
        LocaleStorageInterface $localeStorage,
        LocaleProviderInterface $localeProvider,
    ): void {
        $this->beConstructedWith($channelContext, $localeStorage, $localeProvider);
    }

    function it_is_a_locale_context(): void
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    function it_returns_an_available_active_locale(
        ChannelContextInterface $channelContext,
        LocaleStorageInterface $localeStorage,
        LocaleProviderInterface $localeProvider,
        ChannelInterface $channel,
    ): void {
        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'en_US']);
        $channelContext->getChannel()->willReturn($channel);
        $localeStorage->get($channel)->willReturn('pl_PL');

        $this->getLocaleCode()->shouldReturn('pl_PL');
    }

    function it_throws_an_exception_when_channel_cannot_be_found(
        ChannelContextInterface $channelContext,
        LocaleStorageInterface $localeStorage,
        LocaleProviderInterface $localeProvider,
    ): void {
        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'en_US']);
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);
        $localeStorage->get(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_throws_an_exception_when_storage_is_unavailable(
        ChannelContextInterface $channelContext,
        LocaleStorageInterface $localeStorage,
        LocaleProviderInterface $localeProvider,
        ChannelInterface $channel,
    ): void {
        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'en_US']);
        $channelContext->getChannel()->willReturn($channel);
        $localeStorage->get($channel)->willThrow(StorageUnavailableException::class);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_throws_an_exception_if_locale_taken_from_storage_is_not_available(
        ChannelContextInterface $channelContext,
        LocaleStorageInterface $localeStorage,
        LocaleProviderInterface $localeProvider,
        ChannelInterface $channel,
    ): void {
        $localeProvider->getAvailableLocalesCodes()->willReturn(['en_US', 'en_GB']);
        $channelContext->getChannel()->willReturn($channel);
        $localeStorage->get($channel)->willReturn('pl_PL');

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }
}
