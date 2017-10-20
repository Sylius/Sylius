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
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\ConfigurationLoaderInterface;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;

final class FilesystemConfigurationProviderSpec extends ObjectBehavior
{
    function let(FileLocatorInterface $fileLocator, ConfigurationLoaderInterface $loader): void
    {
        $this->beConstructedWith($fileLocator, $loader, 'configurationfile.json');
    }

    function it_implements_configuration_provider_interface(): void
    {
        $this->shouldImplement(ConfigurationProviderInterface::class);
    }

    function it_provides_loaded_configuration_files(FileLocatorInterface $fileLocator, ConfigurationLoaderInterface $loader): void
    {
        $fileLocator->locateFilesNamed('configurationfile.json')->willReturn([
            '/cristopher/configurationfile.json',
            '/richard/configurationfile.json',
        ]);

        $loader->load('/cristopher/configurationfile.json')->willReturn(['name' => 'cristopher/sylius-theme']);
        $loader->load('/richard/configurationfile.json')->willReturn(['name' => 'richard/sylius-theme']);

        $this->getConfigurations()->shouldReturn([
            ['name' => 'cristopher/sylius-theme'],
            ['name' => 'richard/sylius-theme'],
        ]);
    }

    function it_provides_an_empty_array_if_there_were_no_themes_found(FileLocatorInterface $fileLocator): void
    {
        $fileLocator->locateFilesNamed('configurationfile.json')->willThrow(\InvalidArgumentException::class);

        $this->getConfigurations()->shouldReturn([]);
    }
}
