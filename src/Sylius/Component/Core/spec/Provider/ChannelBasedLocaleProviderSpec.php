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
        $this->beConstructedWith($channelContext);
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

        $enabledLocale->getCode()->willReturn('BTC');

        $this->getAvailableLocalesCodes()->shouldReturn(['BTC']);
    }

    function it_returns_channels_default_locale(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleInterface $locale
    ) {
        $channelContext->getChannel()->willReturn($channel);

        $channel->getDefaultLocale()->willReturn($locale);

        $locale->getCode()->willReturn('BTC');

        $this->getDefaultLocaleCode()->shouldReturn('BTC');
    }

    function it_throws_a_locale_not_found_exception_if_channel_cannot_be_determined(
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getAvailableLocalesCodes');
        $this->shouldThrow(LocaleNotFoundException::class)->during('getDefaultLocaleCode');
    }
}
