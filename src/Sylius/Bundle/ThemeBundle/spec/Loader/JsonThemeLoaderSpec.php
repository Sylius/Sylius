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

use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Loader\JsonThemeLoader;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\PhpSpec\FixtureAwareObjectBehavior;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * @mixin JsonThemeLoader
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class JsonThemeLoaderSpec extends FixtureAwareObjectBehavior
{
    function let(ThemeFactoryInterface $themeFactory)
    {
        $this->beConstructedWith($themeFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Loader\JsonThemeLoader');
    }

    function it_implements_loader_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    function it_loads_valid_theme_file(ThemeFactoryInterface $themeFactory, ThemeInterface $theme)
    {
        $themeFactory->createFromArray([
            "name" => "Sample Theme",
            "slug" => "sylius/sample-theme",
            "description" => "Lorem ipsum dolor sit amet.",
        ])->shouldBeCalled()->willReturn($theme);

        $theme->setPath(realpath(dirname($this->getValidThemeFilePath())))->shouldBeCalled();

        $this->load($this->getValidThemeFilePath())->shouldHaveType('Sylius\Bundle\ThemeBundle\Model\ThemeInterface');
    }

    function it_throws_exception_if_given_file_does_not_exist()
    {
        $this->shouldThrow('\InvalidArgumentException')->duringLoad('/non/existent/path/60861204');
    }

    /**
     * @return string
     */
    private function getValidThemeFilePath()
    {
        return $this->getFixturePath('themes/SampleTheme/theme.json');
    }
}
