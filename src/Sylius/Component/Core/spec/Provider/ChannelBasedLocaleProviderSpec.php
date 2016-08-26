<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Provider\ChannelBasedLocaleProvider;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

/**
 * @mixin ChannelBasedLocaleProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelBasedLocaleProviderSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($channelContext, 'pl_PL');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelBasedLocaleProvider::class);
    }

    function it_is_a_locale_provider()
    {
        $this->shouldImplement(LocaleProviderInterface::class);
    }

    function it_returns_only_channels_enabled_locales_as_available_ones(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleInterface $enabledLocale,
        LocaleInterface $disabledLocale
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $channel->getLocales()->willReturn(new ArrayCollection([
            $enabledLocale->getWrappedObject(),
            $disabledLocale->getWrappedObject(),
        ]));

        $enabledLocale->isEnabled()->willReturn(true);
        $disabledLocale->isEnabled()->willReturn(false);

        $enabledLocale->getCode()->willReturn('en_US');

        $this->getAvailableLocalesCodes()->shouldReturn(['en_US']);
    }

    function it_returns_only_channels_locales_as_defined_ones(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleInterface $enabledLocale,
        LocaleInterface $disabledLocale
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $channel->getLocales()->willReturn(new ArrayCollection([
            $enabledLocale->getWrappedObject(),
            $disabledLocale->getWrappedObject(),
        ]));

        $enabledLocale->isEnabled()->willReturn(true);
        $disabledLocale->isEnabled()->willReturn(false);

        $enabledLocale->getCode()->willReturn('en_US');
        $disabledLocale->getCode()->willReturn('en_GB');

        $this->getDefinedLocalesCodes()->shouldReturn(['en_US', 'en_GB']);
    }

    function it_returns_the_default_locale_as_the_available_one_if_channel_cannot_be_determined(
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->getAvailableLocalesCodes()->shouldReturn(['pl_PL']);
    }

    function it_returns_channels_default_locale(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $channel->getDefaultLocale()->willReturn($locale);

        $locale->getCode()->willReturn('en_US');

        $this->getDefaultLocaleCode()->shouldReturn('en_US');
    }

    function it_returns_the_default_locale_if_channel_cannot_be_determined(
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->getDefaultLocaleCode()->shouldReturn('pl_PL');
    }
}
