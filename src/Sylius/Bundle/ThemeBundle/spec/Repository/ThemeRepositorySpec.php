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
        $theme->getName()->willReturn('example/theme');

        $this->add($theme);

        $this->findAll()->shouldReturn([$theme]);
    }

    function it_returns_theme_by_its_name(ThemeInterface $firstTheme, ThemeInterface $secondTheme)
    {
        $firstTheme->getName()->willReturn('example/frist-theme');
        $secondTheme->getName()->willReturn('example/second-theme');

        $this->add($firstTheme);
        $this->add($secondTheme);

        $this->findOneByName('example/second-theme')->shouldReturn($secondTheme);
    }

    function it_returns_null_if_theme_with_given_name_is_not_found(ThemeInterface $theme)
    {
        $theme->getName()->willReturn('example/default-theme');

        $this->add($theme);

        $this->findOneByName('example/cristopher-theme')->shouldReturn(null);
    }
}
