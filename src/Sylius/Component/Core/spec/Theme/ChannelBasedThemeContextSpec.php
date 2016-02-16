<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Theme;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Theme\ChannelBasedThemeContext;

/**
 * @mixin ChannelBasedThemeContext
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ChannelBasedThemeContextSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext, ThemeRepositoryInterface $themeRepository)
    {
        $this->beConstructedWith($channelContext, $themeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Theme\ChannelBasedThemeContext');
    }

    function it_implements_theme_context_interface()
    {
        $this->shouldImplement(ThemeContextInterface::class);
    }

    function it_returns_a_theme(
        ChannelContextInterface $channelContext,
        ThemeRepositoryInterface $themeRepository,
        ChannelInterface $channel,
        ThemeInterface $theme
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getThemeName()->willReturn('example/theme');

        $themeRepository->findOneByName('example/theme')->willReturn($theme);

        $this->getTheme()->shouldReturn($theme);
    }

    function it_returns_null_if_theme_cannot_be_found(
        ChannelContextInterface $channelContext,
        ThemeRepositoryInterface $themeRepository,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getThemeName()->willReturn('example/theme');

        $themeRepository->findOneByName('example/theme')->willReturn(null);

        $this->getTheme()->shouldReturn(null);
    }

    function it_returns_null_if_there_is_no_theme_set_on_the_channel(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getThemeName()->willReturn(null);

        $this->getTheme()->shouldReturn(null);
    }

    function it_returns_null_if_there_is_no_context(
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->getTheme()->shouldReturn(null);
    }
}
