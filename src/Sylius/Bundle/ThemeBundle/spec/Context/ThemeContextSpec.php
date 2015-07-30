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
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Resolver\ThemeDependenciesResolverInterface;

/**
 * @mixin ThemeContext
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeContextSpec extends ObjectBehavior
{
    function let(ThemeDependenciesResolverInterface $themeDependenciesResolver)
    {
        $this->beConstructedWith($themeDependenciesResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Context\ThemeContext');
    }

    function it_implements_theme_context_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface');
    }

    function it_has_themes(ThemeDependenciesResolverInterface $themeDependenciesResolver, ThemeInterface $theme)
    {
        $theme->getLogicalName()->willReturn("foo/bar1");

        $themeDependenciesResolver->getDependencies($theme)->shouldBeCalled()->willReturn([]);

        $this->getTheme()->shouldReturn(null);
        $this->getThemes()->shouldHaveCount(0);

        $this->setTheme($theme);

        $this->getTheme()->shouldReturn($theme);
        $this->getThemes()->shouldHaveCount(1);

        $this->clear();

        $this->getTheme()->shouldReturn(null);
        $this->getThemes()->shouldHaveCount(0);
    }

    function it_adds_theme_parents_to_context_while_setting_theme(
        ThemeDependenciesResolverInterface $themeDependenciesResolver,
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme
    ) {
        $firstTheme->getLogicalName()->willReturn("foo/bar1");
        $firstTheme->getParentsNames()->willReturn(["foo/bar2"]);

        $secondTheme->getLogicalName()->willReturn("foo/bar2");
        $secondTheme->getParentsNames()->willReturn([]);

        $themeDependenciesResolver->getDependencies($firstTheme)->shouldBeCalled()->willReturn([$secondTheme]);

        $this->setTheme($firstTheme);

        $this->getThemes()->shouldHaveCount(2);
        $this->getThemes()->shouldReturn([
            $firstTheme,
            $secondTheme,
        ]);
    }
}
