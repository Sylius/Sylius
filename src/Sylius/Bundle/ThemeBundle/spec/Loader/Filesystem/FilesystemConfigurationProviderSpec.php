<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Loader\Filesystem;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Loader\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Loader\Filesystem\FilesystemConfigurationProvider;
use Sylius\Bundle\ThemeBundle\Loader\LoaderInterface;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;

/**
 * @mixin FilesystemConfigurationProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class FilesystemConfigurationProviderSpec extends ObjectBehavior
{
    function let(FileLocatorInterface $fileLocator, LoaderInterface $loader)
    {
        $this->beConstructedWith($fileLocator, $loader, 'configurationfile.json');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Loader\Filesystem\FilesystemConfigurationProvider');
    }

    function it_implements_configuration_provider_interface()
    {
        $this->shouldImplement(ConfigurationProviderInterface::class);
    }

    function it_provides_loaded_configuration_files(FileLocatorInterface $fileLocator, LoaderInterface $loader)
    {
        $fileLocator->locateFilesNamed('configurationfile.json')->willReturn([
            '/cristopher/configurationfile.json',
            '/richard/configurationfile.json',
        ]);

        $loader->load('/cristopher/configurationfile.json')->willReturn(['name' => 'cristopher/sylus-theme']);
        $loader->load('/richard/configurationfile.json')->willReturn(['name' => 'richard/sylus-theme']);

        $this->provideAll()->shouldReturn([
            ['name' => 'cristopher/sylus-theme'],
            ['name' => 'richard/sylus-theme'],
        ]);
    }

    function it_provides_an_empty_array_if_there_were_no_themes_found(FileLocatorInterface $fileLocator)
    {
        $fileLocator->locateFilesNamed('configurationfile.json')->willThrow(\InvalidArgumentException::class);

        $this->provideAll()->shouldReturn([]);
    }
}
