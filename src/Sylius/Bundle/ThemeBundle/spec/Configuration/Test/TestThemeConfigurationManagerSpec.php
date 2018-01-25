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

namespace spec\Sylius\Bundle\ThemeBundle\Configuration\Test;

use org\bovigo\vfs\vfsStreamDirectory as VfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper as VfsStreamWrapper;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProcessorInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestThemeConfigurationManagerInterface;

final class TestThemeConfigurationManagerSpec extends ObjectBehavior
{
    function let(ConfigurationProcessorInterface $configurationProcessor): void
    {
        VfsStreamWrapper::register();
        VfsStreamWrapper::setRoot(new VfsStreamDirectory(''));

        $this->beConstructedWith($configurationProcessor, 'vfs://cache/');
    }

    function letGo(): void
    {
        VfsStreamWrapper::unregister();
    }

    function it_implements_test_configuration_manager_interface(): void
    {
        $this->shouldImplement(TestThemeConfigurationManagerInterface::class);
    }

    function it_finds_all_saved_configurations(): void
    {
        $this->findAll()->shouldReturn([]);
    }

    function it_stores_theme_configuration(ConfigurationProcessorInterface $configurationProcessor): void
    {
        $configurationProcessor->process([['name' => 'theme/name']])->willReturn(['name' => 'theme/name']);

        $this->add(['name' => 'theme/name']);

        $this->findAll()->shouldHaveCount(1);
    }

    function its_theme_configurations_can_be_removed(ConfigurationProcessorInterface $configurationProcessor): void
    {
        $configurationProcessor->process([['name' => 'theme/name']])->willReturn(['name' => 'theme/name']);

        $this->add(['name' => 'theme/name']);
        $this->remove('theme/name');

        $this->findAll()->shouldReturn([]);
    }

    function it_clears_all_theme_configurations(ConfigurationProcessorInterface $configurationProcessor): void
    {
        $configurationProcessor->process([['name' => 'theme/name1']])->willReturn(['name' => 'theme/name1']);
        $configurationProcessor->process([['name' => 'theme/name2']])->willReturn(['name' => 'theme/name2']);

        $this->add(['name' => 'theme/name1']);
        $this->add(['name' => 'theme/name2']);

        $this->clear();

        $this->findAll()->shouldReturn([]);
    }

    function it_does_not_throw_any_exception_if_clearing_unexisting_storage(): void
    {
        $this->clear();
    }
}
