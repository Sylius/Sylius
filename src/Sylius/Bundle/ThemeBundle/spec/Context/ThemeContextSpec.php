<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Context\ThemeContext;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @mixin ThemeContext
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeContextSpec extends ObjectBehavior
{
    function let(ThemeHierarchyProviderInterface $themeHierarchyProvider)
    {
        $this->beConstructedWith($themeHierarchyProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Context\ThemeContext');
    }

    function it_implements_theme_context_interface()
    {
        $this->shouldImplement(ThemeContextInterface::class);
    }

    function it_has_theme(ThemeInterface $theme)
    {
        $this->getTheme()->shouldReturn(null);

        $this->setTheme($theme);
        $this->getTheme()->shouldReturn($theme);
    }

    function it_proxies_getting_theme_hierarchy_if_there_is_current_theme(
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        ThemeInterface $theme
    ) {
        $this->setTheme($theme);
        $themeHierarchyProvider->getThemeHierarchy($theme)->willReturn([$theme]);

        $this->getThemeHierarchy()->shouldReturn([$theme]);
    }

    function it_returns_an_empty_array_as_theme_hierarchy_if_there_is_no_current_theme(
        ThemeHierarchyProviderInterface $themeHierarchyProvider
    ) {
        $themeHierarchyProvider->getThemeHierarchy(Argument::any())->shouldNotBeCalled();

        $this->getThemeHierarchy()->shouldReturn([]);
    }
}
