<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Configuration\Test;

use org\bovigo\vfs\vfsStreamDirectory as VfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper as VfsStreamWrapper;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProcessorInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestThemeConfigurationManager;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestThemeConfigurationManagerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TestThemeConfigurationManagerSpec extends ObjectBehavior
{
    function let(ConfigurationProcessorInterface $configurationProcessor)
    {
        VfsStreamWrapper::register();
        VfsStreamWrapper::setRoot(new VfsStreamDirectory(''));

        $this->beConstructedWith($configurationProcessor, 'vfs://cache/');
    }

    function letGo()
    {
        VfsStreamWrapper::unregister();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TestThemeConfigurationManager::class);
    }

    function it_implements_test_configuration_manager_interface()
    {
        $this->shouldImplement(TestThemeConfigurationManagerInterface::class);
    }

    function it_finds_all_saved_configurations()
    {
        $this->findAll()->shouldReturn([]);
    }

    function it_stores_theme_configuration(ConfigurationProcessorInterface $configurationProcessor)
    {
        $configurationProcessor->process([['name' => 'theme/name']])->willReturn(['name' => 'theme/name']);

        $this->add(['name' => 'theme/name']);

        $this->findAll()->shouldHaveCount(1);
    }

    function its_theme_configurations_can_be_removed(ConfigurationProcessorInterface $configurationProcessor)
    {
        $configurationProcessor->process([['name' => 'theme/name']])->willReturn(['name' => 'theme/name']);

        $this->add(['name' => 'theme/name']);
        $this->remove('theme/name');

        $this->findAll()->shouldReturn([]);
    }

    function it_clears_all_theme_configurations(ConfigurationProcessorInterface $configurationProcessor)
    {
        $configurationProcessor->process([['name' => 'theme/name1']])->willReturn(['name' => 'theme/name1']);
        $configurationProcessor->process([['name' => 'theme/name2']])->willReturn(['name' => 'theme/name2']);

        $this->add(['name' => 'theme/name1']);
        $this->add(['name' => 'theme/name2']);

        $this->clear();

        $this->findAll()->shouldReturn([]);
    }

    function it_does_not_throw_any_exception_if_clearing_unexisting_storage()
    {
        $this->clear();
    }
}
