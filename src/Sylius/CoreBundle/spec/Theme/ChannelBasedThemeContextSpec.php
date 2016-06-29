<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\CoreBundle\Theme;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\CoreBundle\Theme\ChannelBasedThemeContext;
use Sylius\ThemeBundle\Context\ThemeContextInterface;
use Sylius\ThemeBundle\Model\ThemeInterface;
use Sylius\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Channel\Context\ChannelContextInterface;
use Sylius\Channel\Context\ChannelNotFoundException;
use Sylius\Core\Model\ChannelInterface;

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
        $this->shouldHaveType('Sylius\CoreBundle\Theme\ChannelBasedThemeContext');
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
        $channel->getThemeName()->willReturn('theme/name');
        $themeRepository->findOneByName('theme/name')->willReturn($theme);

        $this->getTheme()->shouldReturn($theme);
    }

    function it_returns_null_if_channel_has_no_theme(
        ChannelContextInterface $channelContext,
        ThemeRepositoryInterface $themeRepository,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getThemeName()->willReturn(null);
        $themeRepository->findOneByName(null)->willReturn(null);

        $this->getTheme()->shouldReturn(null);
    }

    function it_returns_null_if_there_is_no_channel(
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);

        $this->getTheme()->shouldReturn(null);
    }

    function it_returns_null_if_any_exception_is_thrown_during_getting_the_channel(
        ChannelContextInterface $channelContext
    ) {
        $channelContext->getChannel()->willThrow(\Exception::class);

        $this->getTheme()->shouldReturn(null);
    }
}
