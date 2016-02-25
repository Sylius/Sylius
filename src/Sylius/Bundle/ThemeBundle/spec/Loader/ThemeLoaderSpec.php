<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Configuration\Provider\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Factory\ThemeAuthorFactoryInterface;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Loader\CircularDependencyCheckerInterface;
use Sylius\Bundle\ThemeBundle\Loader\CircularDependencyFoundException;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoader;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoadingFailedException;
use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Zend\Hydrator\HydrationInterface;

/**
 * @mixin ThemeLoader
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeLoaderSpec extends ObjectBehavior
{
    function let(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        ThemeAuthorFactoryInterface $themeAuthorFactory,
        HydrationInterface $themeHydrator,
        CircularDependencyCheckerInterface $circularDependencyChecker
    ) {
        $this->beConstructedWith(
            $configurationProvider,
            $themeFactory,
            $themeAuthorFactory,
            $themeHydrator,
            $circularDependencyChecker
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Loader\ThemeLoader');
    }

    function it_implements_theme_loader_interface()
    {
        $this->shouldImplement(ThemeLoaderInterface::class);
    }

    function it_loads_a_single_theme(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        HydrationInterface $themeHydrator,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $theme
    ) {
        $configurationProvider->getConfigurations()->willReturn([
            ['name' => 'first/theme', 'parents' => [], 'authors' => []]
        ]);

        $themeFactory->createNamed('first/theme')->willReturn($theme);

        $themeHydrator->hydrate(['name' => 'first/theme', 'parents' => [], 'authors' => []], $theme)->willReturn($theme);

        $circularDependencyChecker->check($theme)->shouldBeCalled();

        $this->load()->shouldReturn([$theme]);
    }

    function it_loads_a_theme_with_author(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        ThemeAuthorFactoryInterface $themeAuthorFactory,
        HydrationInterface $themeHydrator,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $theme
    ) {
        $themeAuthor = new ThemeAuthor();

        $configurationProvider->getConfigurations()->willReturn([
            [
                'name' => 'first/theme',
                'parents' => [],
                'authors' => [['name' => 'Richard Rynkowsky']]
            ]
        ]);

        $themeFactory->createNamed('first/theme')->willReturn($theme);
        $themeAuthorFactory->createFromArray(['name' => 'Richard Rynkowsky'])->willReturn($themeAuthor);

        $themeHydrator->hydrate(['name' => 'first/theme', 'parents' => [], 'authors' => [$themeAuthor]], $theme)->willReturn($theme);

        $circularDependencyChecker->check($theme)->shouldBeCalled();

        $this->load()->shouldReturn([$theme]);
    }

    function it_loads_a_theme_with_its_dependency(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        HydrationInterface $themeHydrator,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme
    ) {
        $configurationProvider->getConfigurations()->willReturn([
            ['name' => 'first/theme', 'parents' => ['second/theme'], 'authors' => []],
            ['name' => 'second/theme', 'parents' => [], 'authors' => []],
        ]);

        $themeFactory->createNamed('first/theme')->willReturn($firstTheme);
        $themeFactory->createNamed('second/theme')->willReturn($secondTheme);

        $themeHydrator->hydrate(['name' => 'first/theme', 'parents' => [$secondTheme], 'authors' => []], $firstTheme)->willReturn($firstTheme);
        $themeHydrator->hydrate(['name' => 'second/theme', 'parents' => [], 'authors' => []], $secondTheme)->willReturn($secondTheme);

        $circularDependencyChecker->check($firstTheme)->shouldBeCalled();
        $circularDependencyChecker->check($secondTheme)->shouldBeCalled();

        $this->load()->shouldReturn([$firstTheme, $secondTheme]);
    }

    function it_throws_an_exception_if_requires_not_existing_dependency(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        ThemeInterface $firstTheme
    ) {
        $configurationProvider->getConfigurations()->willReturn([
            ['name' => 'first/theme', 'parents' => ['second/theme'], 'authors' => []],
        ]);

        $themeFactory->createNamed('first/theme')->willReturn($firstTheme);

        $this
            ->shouldThrow(new ThemeLoadingFailedException('Unexisting theme "second/theme" is required by "first/theme".'))
            ->during('load')
        ;
    }

    function it_throws_an_exception_if_there_is_a_circular_dependency_found(
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        HydrationInterface $themeHydrator,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme
    ) {
        $configurationProvider->getConfigurations()->willReturn([
            ['name' => 'first/theme', 'parents' => ['second/theme'], 'authors' => []],
            ['name' => 'second/theme', 'parents' => ['first/theme'], 'authors' => []],
        ]);

        $themeFactory->createNamed('first/theme')->willReturn($firstTheme);
        $themeFactory->createNamed('second/theme')->willReturn($secondTheme);

        $themeHydrator->hydrate(['name' => 'first/theme', 'parents' => [$secondTheme], 'authors' => []], $firstTheme)->willReturn($firstTheme);
        $themeHydrator->hydrate(['name' => 'second/theme', 'parents' => [$firstTheme], 'authors' => []], $secondTheme)->willReturn($secondTheme);

        $circularDependencyChecker->check(Argument::cetera())->willThrow(CircularDependencyFoundException::class);

        $this
            ->shouldThrow(new ThemeLoadingFailedException('Circular dependency found.'))
            ->during('load')
        ;
    }
}
