<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Configuration\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Configuration\Loader\ConfigurationLoaderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Provider\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Provider\FilesystemConfigurationProvider;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;
use Symfony\Component\Config\Resource\ResourceInterface;

/**
 * @mixin FilesystemConfigurationProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class FilesystemConfigurationProviderSpec extends ObjectBehavior
{
    function let(FileLocatorInterface $fileLocator, ConfigurationLoaderInterface $loader)
    {
        $this->beConstructedWith($fileLocator, $loader, 'configurationfile.json');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Configuration\Provider\FilesystemConfigurationProvider');
    }

    function it_implements_configuration_provider_interface()
    {
        $this->shouldImplement(ConfigurationProviderInterface::class);
    }

    function it_provides_loaded_configuration_files(FileLocatorInterface $fileLocator, ConfigurationLoaderInterface $loader)
    {
        $fileLocator->locateFilesNamed('configurationfile.json')->willReturn([
            '/cristopher/configurationfile.json',
            '/richard/configurationfile.json',
        ]);

        $loader->load('/cristopher/configurationfile.json')->willReturn(['name' => 'cristopher/sylus-theme']);
        $loader->load('/richard/configurationfile.json')->willReturn(['name' => 'richard/sylus-theme']);

        $this->getConfigurations()->shouldReturn([
            ['name' => 'cristopher/sylus-theme'],
            ['name' => 'richard/sylus-theme'],
        ]);
    }

    function it_provides_an_empty_array_if_there_were_no_themes_found(FileLocatorInterface $fileLocator)
    {
        $fileLocator->locateFilesNamed('configurationfile.json')->willThrow(\InvalidArgumentException::class);

        $this->getConfigurations()->shouldReturn([]);
    }

    function it_provides_a_list_of_symfony_config_resources_used(FileLocatorInterface $fileLocator)
    {
        $fileLocator->locateFilesNamed('configurationfile.json')->willReturn([
            '/cristopher/configurationfile.json',
            '/richard/configurationfile.json',
        ]);

        $this->getResources()->shouldHaveAllElementsOf(ResourceInterface::class);
    }

    function it_provides_an_empty_list_of_symfony_config_resources_used_if_there_arent_any_found(FileLocatorInterface $fileLocator)
    {
        $fileLocator->locateFilesNamed('configurationfile.json')->willThrow(\InvalidArgumentException::class);

        $this->getResources()->shouldReturn([]);
    }

    /**
     * @return array
     */
    public function getMatchers()
    {
        return [
            'haveAllElementsOf' => function (array $subject, $type) {
                foreach ($subject as $element) {
                    if ($type === get_class($element) || is_subclass_of($element, $type)) {
                        return true;
                    }
                }

                return false;
            },
        ];
    }
}
