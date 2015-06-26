<?php

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

    function it_has_themes(ThemeInterface $theme)
    {
        $theme->getLogicalName()->willReturn("foo/bar1");
        $theme->getParents()->willReturn([]);

        $this->getThemes()->shouldHaveCount(0);

        $this->setTheme($theme);
        $this->getThemes()->shouldHaveCount(1);

        $this->removeAllThemes();
        $this->getThemes()->shouldHaveCount(0);
    }

    function it_overrides_themes_when_new_one_is_set(ThemeInterface $theme)
    {
        $theme->getLogicalName()->willReturn("foo/bar");
        $theme->getParents()->willReturn([]);

        $this->setTheme($theme);
        $this->setTheme($theme);

        $this->getThemes()->shouldHaveCount(1);
    }

    function it_adds_theme_parents_to_context_while_setting_theme(ThemeInterface $firstTheme, ThemeInterface $secondTheme)
    {
        $firstTheme->getLogicalName()->willReturn("foo/bar1");
        $firstTheme->getParents()->willReturn([$secondTheme]);

        $secondTheme->getLogicalName()->willReturn("foo/bar2");
        $secondTheme->getParents()->willReturn([]);

        $this->setTheme($firstTheme);

        $this->getThemes()->shouldHaveCount(2);
        $this->getThemes()->shouldReturn([
            "foo/bar1" => $firstTheme,
            "foo/bar2" => $secondTheme,
        ]);
    }
}
