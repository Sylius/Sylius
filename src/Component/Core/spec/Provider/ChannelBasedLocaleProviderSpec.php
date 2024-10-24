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

namespace spec\Sylius\Component\Core\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

final class ChannelBasedLocaleProviderSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext): void
    {
        $this->beConstructedWith($channelContext, 'pl_PL');
    }

    function it_is_a_locale_provider(): void
    {
        $this->shouldImplement(LocaleProviderInterface::class);
    }

    function it_returns_all_channels_locales_as_available_ones(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleInterface $enabledLocale,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $channel->getLocales()->willReturn(new ArrayCollection([
            $enabledLocale->getWrappedObject(),
        ]));

        $enabledLocale->getCode()->willReturn('en_US');

        $this->getAvailableLocalesCodes()->shouldReturn(['en_US']);
    }

    function it_returns_the_default_locale_as_the_available_one_if_channel_cannot_be_determined(
        ChannelContextInterface $channelContext,
    ): void {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->getAvailableLocalesCodes()->shouldReturn(['pl_PL']);
    }

    function it_returns_channels_default_locale(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleInterface $locale,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $channel->getDefaultLocale()->willReturn($locale);

        $locale->getCode()->willReturn('en_US');

        $this->getDefaultLocaleCode()->shouldReturn('en_US');
    }

    function it_returns_the_default_locale_if_channel_cannot_be_determined(
        ChannelContextInterface $channelContext,
    ): void {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->getDefaultLocaleCode()->shouldReturn('pl_PL');
    }
}
