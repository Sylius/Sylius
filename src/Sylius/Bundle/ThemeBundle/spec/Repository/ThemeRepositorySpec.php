<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Repository;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepository;

/**
 * @mixin ThemeRepository
 * 
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeRepositorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Repository\ThemeRepository');
    }

    function it_implements_theme_repository_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface');
    }
    
    function it_returns_themes(ThemeInterface $theme)
    {
        $theme->getLogicalName()->shouldBeCalled()->willReturn("foo/bar");

        $this->beConstructedWith([$theme]);
        
        $this->findAll()->shouldReturn(["foo/bar" => $theme]);
    }

    function it_returns_theme_by_its_logical_name(ThemeInterface $firstTheme, ThemeInterface $secondTheme)
    {
        $this->beConstructedWith([$firstTheme, $secondTheme]);

        $firstTheme->getLogicalName()->willReturn("foo/bar1");
        $secondTheme->getLogicalName()->willReturn("foo/bar2");

        $this->findByLogicalName("foo/bar2")->shouldReturn($secondTheme);
    }

    function it_returns_null_if_theme_with_given_logical_name_is_not_found(ThemeInterface $theme)
    {
        $this->beConstructedWith([$theme]);

        $theme->getLogicalName()->willReturn("foo/bar");

        $this->findByLogicalName("blah/blah")->shouldReturn(null);
    }
}