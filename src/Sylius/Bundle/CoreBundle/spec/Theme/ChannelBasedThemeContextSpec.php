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

namespace spec\Sylius\Bundle\CoreBundle\Theme;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelBasedThemeContextSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext, ThemeRepositoryInterface $themeRepository): void
    {
        $this->beConstructedWith($channelContext, $themeRepository, null);
    }

    function it_implements_theme_context_interface(): void
    {
        $this->shouldImplement(ThemeContextInterface::class);
    }

    function it_returns_a_theme_if_channel_has_theme(
        ChannelContextInterface $channelContext,
        ThemeRepositoryInterface $themeRepository,
        ChannelInterface $channel,
        ThemeInterface $theme
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getThemeName()->willReturn('theme/name');
        $themeRepository->findOneByName('theme/name')->willReturn($theme);

        $this->getTheme()->shouldReturn($theme);
    }

    function it_returns_a_theme_if_channel_has_no_theme_but_default_theme_is_set(
        ChannelContextInterface $channelContext,
        ThemeRepositoryInterface $themeRepository,
        ChannelInterface $channel,
        ThemeInterface $theme
    ): void {
        $this->beConstructedWith($channelContext, $themeRepository, 'theme/name');
        $channelContext->getChannel()->willReturn($channel);
        $channel->getThemeName()->willReturn(null);
        $themeRepository->findOneByName('theme/name')->willReturn($theme);

        $this->getTheme()->shouldReturn($theme);
    }

    function it_returns_null_if_channel_has_no_theme_and_default_theme_is_not_set(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getThemeName()->willReturn(null);

        $this->getTheme()->shouldReturn(null);
    }

    function it_returns_a_theme_if_there_is_no_channel_but_default_theme_is_set(
        ChannelContextInterface $channelContext,
        ThemeRepositoryInterface $themeRepository,
        ThemeInterface $theme
    ): void {
        $this->beConstructedWith($channelContext, $themeRepository, 'theme/name');
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);
        $themeRepository->findOneByName('theme/name')->willReturn($theme);

        $this->getTheme()->shouldReturn($theme);
    }

    function it_returns_null_if_there_is_no_channel_and_default_theme_is_not_set(
        ChannelContextInterface $channelContext
    ): void {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->getTheme()->shouldReturn(null);
    }

    function it_returns_a_theme_if_any_exception_is_thrown_during_getting_the_channel_but_default_theme_is_set(
        ChannelContextInterface $channelContext,
        ThemeRepositoryInterface $themeRepository,
        ThemeInterface $theme
    ): void {
        $this->beConstructedWith($channelContext, $themeRepository, 'theme/name');
        $channelContext->getChannel()->willThrow(\Exception::class);
        $themeRepository->findOneByName('theme/name')->willReturn($theme);

        $this->getTheme()->shouldReturn($theme);
    }

    function it_returns_null_if_any_exception_is_thrown_during_getting_the_channel_and_default_theme_is_not_set(
        ChannelContextInterface $channelContext
    ): void {
        $channelContext->getChannel()->willThrow(\Exception::class);

        $this->getTheme()->shouldReturn(null);
    }
}
