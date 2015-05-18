<?php

namespace spec\Sylius\Bundle\ThemeBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\Theme;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @mixin Theme
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Model\Theme');
    }

    function it_implements_theme_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ThemeBundle\Model\ThemeInterface');
    }

    function it_has_name()
    {
        $this->setName("Foo Bar");
        $this->getName()->shouldReturn("Foo Bar");
    }

    function it_has_logical_name()
    {
        $this->setLogicalName("foo/bar");
        $this->getLogicalName()->shouldReturn("foo/bar");
    }

    function it_has_description()
    {
        $this->setDescription("Lorem ipsum.");
        $this->getDescription()->shouldReturn("Lorem ipsum.");
    }

    function it_has_path()
    {
        $this->setPath("/foo/bar");
        $this->getPath()->shouldReturn("/foo/bar");
    }

    function it_says_whether_themes_are_equal(ThemeInterface $firstTheme, ThemeInterface $secondTheme, ThemeInterface $thirdTheme)
    {
        $this->setLogicalName("same/name");
        $this->setPath("/same/path");
        
        $firstTheme->getLogicalName()->willReturn("same/name");
        $firstTheme->getPath()->willReturn("/different/path");

        $secondTheme->getLogicalName()->willReturn("different/name");
        $secondTheme->getPath()->willReturn("/same/path");

        $thirdTheme->getLogicalName()->willReturn("different/name");
        $thirdTheme->getPath()->willReturn("/different/path");
        
        $this->equals($firstTheme)->shouldReturn(true);
        $this->equals($secondTheme)->shouldReturn(true);
        $this->equals($thirdTheme)->shouldReturn(false);
    }
}
