<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Context\Setup\ThemeContext;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @mixin ThemeContext
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        ThemeFactoryInterface $themeFactory,
        ThemeRepositoryInterface $themeRepository
    ) {
        $this->beConstructedWith($sharedStorage, $themeFactory, $themeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\ThemeContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_defines_a_theme(
        SharedStorageInterface $sharedStorage,
        ThemeFactoryInterface $themeFactory,
        ThemeRepositoryInterface $themeRepository,
        ThemeInterface $theme
    ) {
        $themeFactory->createFromArray(Argument::that(function (array $array) {
            return isset($array['name'], $array['path']) && 'theme/name' === $array['name'];
        }))->willReturn($theme);

        $themeRepository->add($theme)->shouldBeCalled();
        $sharedStorage->set('theme', $theme)->shouldBeCalled();

        $this->thereIsThemeDefined('theme/name');
    }

    function it_defines_themes(
        SharedStorageInterface $sharedStorage,
        ThemeFactoryInterface $themeFactory,
        ThemeRepositoryInterface $themeRepository,
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme
    ) {
        $themeFactory->createFromArray(Argument::that(function (array $array) {
            return isset($array['name'], $array['path']) && 'first/theme-name' === $array['name'];
        }))->willReturn($firstTheme);
        $themeFactory->createFromArray(Argument::that(function (array $array) {
            return isset($array['name'], $array['path']) && 'second/theme-name' === $array['name'];
        }))->willReturn($secondTheme);

        $themeRepository->add($firstTheme)->shouldBeCalled();
        $themeRepository->add($secondTheme)->shouldBeCalled();
        $sharedStorage->set('theme', $firstTheme)->shouldBeCalled();
        $sharedStorage->set('theme', $secondTheme)->shouldBeCalled();

        $this->thereAreThemesDefined('first/theme-name', 'second/theme-name');
    }
}
