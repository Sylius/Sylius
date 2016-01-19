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
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Filesystem\FilesystemInterface;
use Sylius\Bundle\ThemeBundle\Loader\JsonThemeLoader;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * @mixin JsonThemeLoader
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class JsonThemeLoaderSpec extends ObjectBehavior
{
    function let(FilesystemInterface $filesystem, ThemeFactoryInterface $themeFactory)
    {
        $this->beConstructedWith($filesystem, $themeFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Loader\JsonThemeLoader');
    }

    function it_implements_loader_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    function it_loads_valid_theme_file(FilesystemInterface $filesystem, ThemeFactoryInterface $themeFactory, ThemeInterface $theme)
    {
        $filesystem->exists('/themes/SampleTheme/theme.json')->willReturn(true);
        $filesystem->getFileContents('/themes/SampleTheme/theme.json')->willReturn(
            '{ "name": "Sample Theme", "slug": "sylius/sample-theme", "description": "Lorem ipsum dolor sit amet." }'
        );

        $themeFactory->createFromArray([
            "name" => "Sample Theme",
            "slug" => "sylius/sample-theme",
            "description" => "Lorem ipsum dolor sit amet.",
        ])->willReturn($theme);

        $theme->setPath('/themes/SampleTheme')->shouldBeCalled();

        $this->load('/themes/SampleTheme/theme.json')->shouldReturn($theme);
    }

    function it_throws_exception_if_given_file_does_not_exist(FilesystemInterface $filesystem)
    {
        $filesystem->exists('/non/existent/path/60861204')->willReturn(false);

        $this->shouldThrow('\InvalidArgumentException')->duringLoad('/non/existent/path/60861204');
    }
}
