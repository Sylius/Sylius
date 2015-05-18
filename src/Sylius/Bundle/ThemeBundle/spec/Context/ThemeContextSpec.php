<?php

namespace spec\Sylius\Bundle\ThemeBundle\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Context\ThemeContext;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @mixin ThemeContext
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Context\ThemeContext');
    }

    function it_implements_theme_context_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface');
    }

    function it_has_themes(ThemeInterface $firstTheme, ThemeInterface $secondTheme)
    {
        $firstTheme->equals($firstTheme)->willReturn(true);
        $firstTheme->equals($secondTheme)->willReturn(false);
        $firstTheme->getLogicalName()->willReturn("foo/bar1");

        $secondTheme->equals($secondTheme)->willReturn(true);
        $secondTheme->equals($firstTheme)->willReturn(false);
        $secondTheme->getLogicalName()->willReturn("foo/bar2");

        $this->getThemes()->shouldHaveCount(0);

        $this->hasTheme($firstTheme)->shouldReturn(false);
        $this->hasTheme($secondTheme)->shouldReturn(false);

        $this->addTheme($firstTheme);

        $this->getThemes()->shouldHaveCount(1);
        $this->hasTheme($firstTheme)->shouldReturn(true);
        $this->hasTheme($secondTheme)->shouldReturn(false);

        $this->removeTheme($firstTheme);

        $this->getThemes()->shouldHaveCount(0);
        $this->hasTheme($firstTheme)->shouldReturn(false);
        $this->hasTheme($secondTheme)->shouldReturn(false);
    }

    function it_does_not_allow_to_add_theme_twice(ThemeInterface $theme)
    {
        $theme->equals($theme)->willReturn(true);
        $theme->getLogicalName()->willReturn("foo/bar");

        $this->addTheme($theme);
        $this->addTheme($theme);

        $this->getThemes()->shouldHaveCount(1);
    }

    function it_lists_themes_sorted_by_priority_in_ascending_order(
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme,
        ThemeInterface $thirdTheme,
        ThemeInterface $fourthTheme
    ) {
        $firstTheme->equals(Argument::any())->willReturn(false);
        $firstTheme->getLogicalName()->willReturn("foo/bar1");

        $secondTheme->equals(Argument::any())->willReturn(false);
        $secondTheme->getLogicalName()->willReturn("foo/bar2");

        $thirdTheme->equals(Argument::any())->willReturn(false);
        $thirdTheme->getLogicalName()->willReturn("foo/bar3");

        $fourthTheme->equals(Argument::any())->willReturn(false);
        $fourthTheme->getLogicalName()->willReturn("foo/bar4");

        $this->addTheme($firstTheme);
        $this->addTheme($secondTheme, -5);
        $this->addTheme($thirdTheme, 5);
        $this->addTheme($fourthTheme);

        $this->getThemesSortedByPriorityInAscendingOrder()->shouldReturn([
            $secondTheme,
            $fourthTheme,
            $firstTheme,
            $thirdTheme,
        ]);
    }

    function it_lists_themes_sorted_by_priority_in_descending_order(
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme,
        ThemeInterface $thirdTheme,
        ThemeInterface $fourthTheme
    ) {
        $firstTheme->equals(Argument::any())->willReturn(false);
        $firstTheme->getLogicalName()->willReturn("foo/bar1");

        $secondTheme->equals(Argument::any())->willReturn(false);
        $secondTheme->getLogicalName()->willReturn("foo/bar2");

        $thirdTheme->equals(Argument::any())->willReturn(false);
        $thirdTheme->getLogicalName()->willReturn("foo/bar3");

        $fourthTheme->equals(Argument::any())->willReturn(false);
        $fourthTheme->getLogicalName()->willReturn("foo/bar4");

        $this->addTheme($firstTheme);
        $this->addTheme($secondTheme, -5);
        $this->addTheme($thirdTheme, 5);
        $this->addTheme($fourthTheme);

        $this->getThemesSortedByPriorityInDescendingOrder()->shouldReturn([
            $thirdTheme,
            $firstTheme,
            $fourthTheme,
            $secondTheme,
        ]);
    }

    function it_shows_theme_priority(ThemeInterface $theme)
    {
        $theme->getLogicalName()->willReturn("foo/bar");

        $this->addTheme($theme, 10);

        $this->getThemePriority("foo/bar")->shouldReturn(10);
    }

    function it_shows_themes_priority(ThemeInterface $firstTheme, ThemeInterface $secondTheme)
    {
        $firstTheme->equals(Argument::any())->willReturn(false);
        $firstTheme->getLogicalName()->willReturn("foo/bar1");

        $secondTheme->equals(Argument::any())->willReturn(false);
        $secondTheme->getLogicalName()->willReturn("foo/bar2");

        $this->addTheme($firstTheme, 5);
        $this->addTheme($secondTheme, 50);

        $this->getThemesPriorities()->shouldReturn([
            "foo/bar1" => 5,
            "foo/bar2" => 50
        ]);
    }
}
