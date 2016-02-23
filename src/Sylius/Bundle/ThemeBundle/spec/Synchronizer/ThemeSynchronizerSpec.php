<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Synchronizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Loader\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Provider\ThemeProviderInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Synchronizer\CircularDependencyCheckerInterface;
use Sylius\Bundle\ThemeBundle\Synchronizer\CircularDependencyFoundException;
use Sylius\Bundle\ThemeBundle\Synchronizer\SynchronizationFailedException;
use Sylius\Bundle\ThemeBundle\Synchronizer\ThemeSynchronizer;
use Sylius\Bundle\ThemeBundle\Synchronizer\ThemeSynchronizerInterface;
use Zend\Hydrator\HydrationInterface;

/**
 * @mixin ThemeSynchronizer
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeSynchronizerSpec extends ObjectBehavior
{
    function let(
        ConfigurationProviderInterface $configurationProvider,
        ThemeProviderInterface $themeProvider,
        HydrationInterface $themeHydrator,
        ThemeRepositoryInterface $themeRepository,
        CircularDependencyCheckerInterface $circularDependencyChecker
    ) {
        $this->beConstructedWith(
            $configurationProvider,
            $themeProvider,
            $themeHydrator,
            $themeRepository,
            $circularDependencyChecker
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Synchronizer\ThemeSynchronizer');
    }

    function it_implements_theme_synchronizer_interface()
    {
        $this->shouldImplement(ThemeSynchronizerInterface::class);
    }

    function it_synchronizes_a_single_theme(
        ConfigurationProviderInterface $configurationProvider,
        ThemeProviderInterface $themeProvider,
        HydrationInterface $themeHydrator,
        ThemeRepositoryInterface $themeRepository,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $firstTheme
    ) {
        $configurationProvider->getConfigurations()->willReturn([
            ['name' => 'first/theme', 'parents' => []],
        ]);

        $themeProvider->getNamed('first/theme')->willReturn($firstTheme);

        $themeHydrator->hydrate(['name' => 'first/theme', 'parents' => []], $firstTheme)->willReturn($firstTheme);

        $circularDependencyChecker->check($firstTheme)->shouldBeCalled();

        $themeRepository->add($firstTheme);

        $this->synchronize();
    }

    function it_synchronizes_a_theme_with_its_dependency(
        ConfigurationProviderInterface $configurationProvider,
        ThemeProviderInterface $themeProvider,
        HydrationInterface $themeHydrator,
        ThemeRepositoryInterface $themeRepository,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme
    ) {
        $configurationProvider->getConfigurations()->willReturn([
            ['name' => 'first/theme', 'parents' => ['second/theme']],
            ['name' => 'second/theme', 'parents' => []],
        ]);

        $themeProvider->getNamed('first/theme')->willReturn($firstTheme);
        $themeProvider->getNamed('second/theme')->willReturn($secondTheme);

        $themeHydrator->hydrate(['name' => 'first/theme', 'parents' => [$secondTheme]], $firstTheme)->willReturn($firstTheme);
        $themeHydrator->hydrate(['name' => 'second/theme', 'parents' => []], $secondTheme)->willReturn($secondTheme);

        $circularDependencyChecker->check($firstTheme)->shouldBeCalled();
        $circularDependencyChecker->check($secondTheme)->shouldBeCalled();

        $themeRepository->add($firstTheme);
        $themeRepository->add($secondTheme);

        $this->synchronize();
    }

    function it_throws_an_exception_if_requires_not_existing_dependency(
        ConfigurationProviderInterface $configurationProvider,
        ThemeProviderInterface $themeProvider,
        ThemeInterface $firstTheme
    ) {
        $configurationProvider->getConfigurations()->willReturn([
            ['name' => 'first/theme', 'parents' => ['second/theme']],
        ]);

        $themeProvider->getNamed('first/theme')->willReturn($firstTheme);

        $this
            ->shouldThrow(new SynchronizationFailedException('Unexisting theme "second/theme" is required by "first/theme".'))
            ->during('synchronize')
        ;
    }

    function it_throws_an_exception_if_there_is_a_circular_dependency_found(
        ConfigurationProviderInterface $configurationProvider,
        ThemeProviderInterface $themeProvider,
        HydrationInterface $themeHydrator,
        ThemeRepositoryInterface $themeRepository,
        CircularDependencyCheckerInterface $circularDependencyChecker,
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme
    ) {
        $configurationProvider->getConfigurations()->willReturn([
            ['name' => 'first/theme', 'parents' => ['second/theme']],
            ['name' => 'second/theme', 'parents' => ['first/theme']],
        ]);

        $themeProvider->getNamed('first/theme')->willReturn($firstTheme);
        $themeProvider->getNamed('second/theme')->willReturn($secondTheme);

        $themeHydrator->hydrate(['name' => 'first/theme', 'parents' => [$secondTheme]], $firstTheme)->willReturn($firstTheme);
        $themeHydrator->hydrate(['name' => 'second/theme', 'parents' => [$firstTheme]], $secondTheme)->willReturn($secondTheme);

        $circularDependencyChecker->check(Argument::cetera())->willThrow(CircularDependencyFoundException::class);

        $themeRepository->add(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(new SynchronizationFailedException('Circular dependency found.'))
            ->during('synchronize')
        ;
    }
}
