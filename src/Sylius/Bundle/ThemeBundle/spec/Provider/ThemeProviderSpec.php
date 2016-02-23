<?php

namespace spec\Sylius\Bundle\ThemeBundle\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Provider\ThemeProvider;
use Sylius\Bundle\ThemeBundle\Provider\ThemeProviderInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * @mixin ThemeProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeProviderSpec extends ObjectBehavior
{
    function let(ThemeRepositoryInterface $themeRepository, ThemeFactoryInterface $themeFactory)
    {
        $this->beConstructedWith($themeRepository, $themeFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Provider\ThemeProvider');
    }

    function it_implements_theme_provider_interface()
    {
        $this->shouldImplement(ThemeProviderInterface::class);
    }

    function it_returns_existing_theme_if_found(ThemeRepositoryInterface $themeRepository, ThemeInterface $theme)
    {
        $themeRepository->findOneByName('example/theme')->willReturn($theme);

        $this->getNamed('example/theme')->shouldReturn($theme);
    }

    function it_creates_a_new_theme_if_not_found(
        ThemeRepositoryInterface $themeRepository,
        ThemeFactoryInterface $themeFactory,
        ThemeInterface $theme
    ) {
        $themeRepository->findOneByName('example/theme')->willReturn(null);

        $themeFactory->createNamed('example/theme')->willReturn($theme);

        $this->getNamed('example/theme')->shouldReturn($theme);
    }
}
