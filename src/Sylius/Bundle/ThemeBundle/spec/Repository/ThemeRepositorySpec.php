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
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

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
        $this->shouldImplement(ThemeRepositoryInterface::class);
    }
    
    function it_returns_themes(ThemeInterface $theme)
    {
        $theme->getSlug()->willReturn("foo/bar");

        $this->beConstructedWith([$theme]);
        
        $this->findAll()->shouldReturn([$theme]);
    }

    function it_returns_theme_by_its_slug(ThemeInterface $firstTheme, ThemeInterface $secondTheme)
    {
        $firstTheme->getSlug()->willReturn("foo/bar1");
        $secondTheme->getSlug()->willReturn("foo/bar2");

        $this->beConstructedWith([$firstTheme, $secondTheme]);

        $this->findOneBySlug("foo/bar2")->shouldReturn($secondTheme);
    }

    function it_returns_null_if_theme_with_given_slug_is_not_found(ThemeInterface $theme)
    {
        $theme->getSlug()->willReturn("foo/bar");

        $this->beConstructedWith([$theme]);

        $this->findOneBySlug("blah/blah")->shouldReturn(null);
    }
}
