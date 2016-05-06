<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Settings;

use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeSettingsManager implements ThemeSettingsManagerInterface
{
    /**
     * @var SettingsManagerInterface
     */
    private $decoratedSettingsManager;

    /**
     * @var ServiceRegistryInterface
     */
    private $schemaRegistry;

    /**
     * @var ThemeSettingsSchemaProviderInterface
     */
    private $themeSettingsSchemaProvider;

    /**
     * @param SettingsManagerInterface $decoratedSettingsManager
     * @param ServiceRegistryInterface $schemaRegistry
     * @param ThemeSettingsSchemaProviderInterface $themeSettingsSchemaProvider
     */
    public function __construct(
        SettingsManagerInterface $decoratedSettingsManager,
        ServiceRegistryInterface $schemaRegistry,
        ThemeSettingsSchemaProviderInterface $themeSettingsSchemaProvider
    ) {
        $this->decoratedSettingsManager = $decoratedSettingsManager;
        $this->schemaRegistry = $schemaRegistry;
        $this->themeSettingsSchemaProvider = $themeSettingsSchemaProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ThemeInterface $theme, $namespace = null)
    {
        $schemaAlias = sprintf('theme_%s', $theme->getName());

        if (!$this->schemaRegistry->has($schemaAlias)) {
            $schema = $this->themeSettingsSchemaProvider->getSchema($theme);

            $this->schemaRegistry->register($schemaAlias, $schema);
        }

        return $this->decoratedSettingsManager->load($schemaAlias, $namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function save(SettingsInterface $settings)
    {
        $this->decoratedSettingsManager->save($settings);
    }
}
