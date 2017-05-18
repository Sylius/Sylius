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
use Sylius\Component\Core\Locale\Context\StorageBasedLocaleContext;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Provider\LocaleProviderInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class StorageBasedLocaleContextSpec extends ObjectBehavior
{
    function let(
        ChannelContextInterface $channelContext,
        LocaleStorageInterface $localeStorage,
        LocaleProviderInterface $localeProvider
    ) {
        $this->beConstructedWith($channelContext, $localeStorage, $localeProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(StorageBasedLocaleContext::class);
    }

    function it_is_a_locale_context()
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    function it_returns_an_available_active_locale(
        ChannelContextInterface $channelContext,
        LocaleStorageInterface $localeStorage,
        LocaleProviderInterface $localeProvider,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $localeStorage->get($channel)->willReturn('pl_PL');

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'en_US']);

        $this->getLocaleCode()->shouldReturn('pl_PL');
    }

    function it_throws_an_exception_if_locale_taken_from_storage_is_not_available(
        ChannelContextInterface $channelContext,
        LocaleStorageInterface $localeStorage,
        LocaleProviderInterface $localeProvider,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $localeStorage->get($channel)->willReturn('pl_PL');

        $localeProvider->getAvailableLocalesCodes()->willReturn(['en_US', 'en_UK']);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }
}
