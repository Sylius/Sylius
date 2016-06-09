<?php

namespace Sylius\Bundle\FixturesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SyliusFixturesExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');

        $this->registerSuites($config, $container);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function registerSuites(array $config, ContainerBuilder $container)
    {
        $suiteRegistry = $container->findDefinition('sylius_fixtures.suite_registry');
        foreach ($config['suites'] as $suiteName => $suiteConfiguration) {
            $suiteRegistry->addMethodCall('addSuite', [$suiteName, $suiteConfiguration]);
        }
    }
}
