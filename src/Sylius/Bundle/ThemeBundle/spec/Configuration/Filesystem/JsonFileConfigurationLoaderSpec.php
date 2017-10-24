<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ThemeBundle\Configuration\Filesystem;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\ConfigurationLoaderInterface;
use Sylius\Bundle\ThemeBundle\Filesystem\FilesystemInterface;

final class JsonFileConfigurationLoaderSpec extends ObjectBehavior
{
    function let(FilesystemInterface $filesystem): void
    {
        $this->beConstructedWith($filesystem);
    }

    function it_implements_configuration_loader_interface(): void
    {
        $this->shouldImplement(ConfigurationLoaderInterface::class);
    }

    function it_loads_json_file(FilesystemInterface $filesystem): void
    {
        $filesystem->exists('/directory/composer.json')->willReturn(true);

        $filesystem->getFileContents('/directory/composer.json')->willReturn('{ "name": "example/sylius-theme" }');

        $this->load('/directory/composer.json')->shouldReturn([
            'path' => '/directory',
            'name' => 'example/sylius-theme',
        ]);
    }

    function it_throws_an_exception_if_file_does_not_exist(FilesystemInterface $filesystem): void
    {
        $filesystem->exists('composer.json')->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('load', ['composer.json']);
    }
}
