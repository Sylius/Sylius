<?php

namespace spec\Sylius\Bundle\ThemeBundle\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Resolver\ThemeDependenciesResolver;

/**
 * @mixin ThemeDependenciesResolver
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeDependenciesResolverSpec extends ObjectBehavior
{
    function let(ThemeRepositoryInterface $themeRepository)
    {
        $this->beConstructedWith($themeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Resolver\ThemeDependenciesResolver');
    }

    function it_implements_theme_dependencies_resolver_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ThemeBundle\Resolver\ThemeDependenciesResolverInterface');
    }

    function it_resolves_themes(ThemeRepositoryInterface $themeRepository, ThemeInterface $firstTheme, ThemeInterface $secondTheme)
    {
        $firstTheme->getLogicalName()->willReturn("foo/bar1");
        $firstTheme->getParentsNames()->willReturn(["foo/bar2"]);

        $secondTheme->getLogicalName()->willReturn("foo/bar2");
        $secondTheme->getParentsNames()->willReturn([]);

        $themeRepository->findByLogicalName("foo/bar1")->willReturn($firstTheme);
        $themeRepository->findByLogicalName("foo/bar2")->willReturn($secondTheme);

        $this->getDependencies($firstTheme)->shouldReturn([$secondTheme]);
    }

}