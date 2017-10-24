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

namespace Sylius\Bundle\FixturesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SyliusFixturesExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $this->registerSuites($config, $container);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $extensionsNamesToConfigurationFiles = [
            'doctrine' => 'doctrine/orm.xml',
            'doctrine_mongodb' => 'doctrine/mongodb-odm.xml',
            'doctrine_phpcr' => 'doctrine/phpcr-odm.xml',
        ];

        foreach ($extensionsNamesToConfigurationFiles as $extensionName => $configurationFile) {
            if (!$container->hasExtension($extensionName)) {
                continue;
            }

            $loader->load('services/integrations/' . $configurationFile);
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function registerSuites(array $config, ContainerBuilder $container): void
    {
        $suiteRegistry = $container->findDefinition('sylius_fixtures.suite_registry');
        foreach ($config['suites'] as $suiteName => $suiteConfiguration) {
            $suiteRegistry->addMethodCall('addSuite', [$suiteName, $suiteConfiguration]);
        }
    }
}
