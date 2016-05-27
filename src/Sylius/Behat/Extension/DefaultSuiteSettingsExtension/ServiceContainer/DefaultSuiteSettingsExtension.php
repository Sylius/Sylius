<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\DefaultSuiteSettingsExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class DefaultSuiteSettingsExtension implements Extension
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'sylius_default_suite_settings';
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder->prototype('variable');
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $container->setParameter('suite.generic.default_settings', array_merge(
            $container->getParameter('suite.generic.default_settings'),
            $config
        ));
    }

    public function process(ContainerBuilder $container)
    {
    }
}
