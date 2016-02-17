<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProvider;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * @mixin ThemeHierarchyProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeHierarchyProviderSpec extends ObjectBehavior
{
    function let(ThemeRepositoryInterface $themeRepository)
    {
        $this->beConstructedWith($themeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeDependenciesResolver');
    }

    function it_implements_theme_hierarchy_provider_interface()
    {
        $this->shouldImplement(ThemeHierarchyProviderInterface::class);
    }

    function it_returns_theme_list_in_hierarchized_order(ThemeRepositoryInterface $themeRepository, ThemeInterface $firstTheme, ThemeInterface $secondTheme)
    {
        $firstTheme->getName()->willReturn('foo/bar1');
        $firstTheme->getParentsNames()->willReturn(['foo/bar2']);

        $secondTheme->getName()->willReturn('foo/bar2');
        $secondTheme->getParentsNames()->willReturn([]);

        $themeRepository->findOneByName('foo/bar1')->willReturn($firstTheme);
        $themeRepository->findOneByName('foo/bar2')->willReturn($secondTheme);

        $this->getThemeHierarchy($firstTheme)->shouldReturn([$firstTheme, $secondTheme]);
    }
}
