<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Settings;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Settings\ThemeSettingsManager;
use Sylius\Bundle\ThemeBundle\Settings\ThemeSettingsManagerInterface;
use Sylius\Bundle\ThemeBundle\Settings\ThemeSettingsSchemaProviderInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @mixin ThemeSettingsManager
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeSettingsManagerSpec extends ObjectBehavior
{
    function let(
        SettingsManagerInterface $decoratedSettingsManager,
        ServiceRegistryInterface $schemaRegistry,
        ThemeSettingsSchemaProviderInterface $themeSettingsSchemaProvider
    ) {
        $this->beConstructedWith($decoratedSettingsManager, $schemaRegistry, $themeSettingsSchemaProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Settings\ThemeSettingsManager');
    }

    function it_implements_theme_settings_manager_interface()
    {
        $this->shouldImplement(ThemeSettingsManagerInterface::class);
    }

    function it_transforms_theme_to_schema_alias_during_loading_settings(
        SettingsManagerInterface $decoratedSettingsManager,
        ServiceRegistryInterface $schemaRegistry,
        ThemeInterface $theme,
        SettingsInterface $settings
    ) {
        $theme->getName()->willReturn('theme/name');

        $schemaRegistry->has('theme_theme/name')->willReturn(true);
        $schemaRegistry->register(Argument::cetera())->shouldNotBeCalled();

        $decoratedSettingsManager->load('theme_theme/name', null)->willReturn($settings);

        $this->load($theme)->shouldReturn($settings);
    }

    function it_registers_theme_schema_alias_if_not_exists_during_loading_settings(
        SettingsManagerInterface $decoratedSettingsManager,
        ServiceRegistryInterface $schemaRegistry,
        ThemeSettingsSchemaProviderInterface $themeSettingsSchemaProvider,
        ThemeInterface $theme,
        SettingsInterface $settings,
        SchemaInterface $schema
    ) {
        $theme->getName()->willReturn('theme/name');

        $schemaRegistry->has('theme_theme/name')->willReturn(false);

        $themeSettingsSchemaProvider->getSchema($theme)->willReturn($schema);

        $schemaRegistry->register('theme_theme/name', $schema)->shouldBeCalled();

        $decoratedSettingsManager->load('theme_theme/name', null)->willReturn($settings);

        $this->load($theme)->shouldReturn($settings);
    }

    function it_delegates_saving_settings_to_decorated_settings_manager(
        SettingsManagerInterface $decoratedSettingsManager,
        SettingsInterface $settings
    ) {
        $decoratedSettingsManager->save($settings)->shouldBeCalled();

        $this->save($settings);
    }
}
