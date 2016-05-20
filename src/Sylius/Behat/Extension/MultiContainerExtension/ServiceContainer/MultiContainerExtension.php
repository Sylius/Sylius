<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\MultiContainerExtension\ServiceContainer;

use Behat\Testwork\Environment\ServiceContainer\EnvironmentExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Sylius\Behat\Extension\MultiContainerExtension\ContainerConfiguration;
use Sylius\Behat\Extension\MultiContainerExtension\Context\Environment\Handler\ContextServiceEnvironmentHandler;
use Sylius\Behat\Extension\MultiContainerExtension\ContextRegistry;
use Sylius\Behat\Extension\MultiContainerExtension\Loader\XmlFileLoader;
use Sylius\Behat\Extension\MultiContainerExtension\ScopeManipulator;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Component\ClassLoader\ClassLoader;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Scope;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class MultiContainerExtension implements Extension
{
    /**
     * @var ContainerConfiguration
     */
    private $containerConfiguration;

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'sylius_multi_container';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        $this->containerConfiguration = new ContainerConfiguration('behat');
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $config = $builder->children();
        $config
            ->arrayNode('imports')
                ->performNoDeepMerging()
                ->prototype('scalar')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->enableLazyServicesSupport($container);

        $this->registerAutoloader($container);
        $this->loadImportedServicesFiles($container, $config);

        $this->loadContextRegistry($container);
        $this->loadScopeManipulator($container);

        $this->declareScenarioScope($container);
        $this->loadEnvironmentHandler($container);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $contextRegistryDefinition = $container->findDefinition('sylius_multi_container.context_registry');
        $taggedServices = $container->findTaggedServiceIds('sylius.behat.context');

        foreach ($taggedServices as $id => $tags) {
            $contextRegistryDefinition->addMethodCall(
                'add',
                [$id, $container->findDefinition($id)->getClass()]
            );
        }
    }

    /**
     * @param string $containerName
     * @param string $containerId
     */
    public function addContainer($containerName, $containerId)
    {
        $this->containerConfiguration->addContainer($containerName, $containerId);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function registerAutoloader(ContainerBuilder $container)
    {
        $classLoader = new ClassLoader();
        foreach ($container->getParameter('class_loader.prefixes') as $namespace => $path) {
            $classLoader->addPrefix($namespace, str_replace('%paths.base%', $container->getParameter('paths.base'), $path));
        }
        $classLoader->register();
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    private function loadImportedServicesFiles(ContainerBuilder $container, array $config)
    {
        $basePath = $container->getParameter('paths.base');
        $xmlLoader = new XmlFileLoader($container, new FileLocator($basePath), $this->containerConfiguration);
        foreach ($config['imports'] as $file) {
            $xmlLoader->load($file);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function declareScenarioScope(ContainerBuilder $container)
    {
        if (!$container->hasScope('scenario')) {
            $container->addScope(new Scope('scenario'));
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadEnvironmentHandler(ContainerBuilder $container)
    {
        $definition = new Definition(ContextServiceEnvironmentHandler::class, [
            new Reference('sylius_multi_container.context_registry'),
            new Reference('service_container')
        ]);
        $definition->addTag(EnvironmentExtension::HANDLER_TAG, ['priority' => 128]);

        $container->setDefinition('sylius_multi_container.environment_handler.context_service', $definition);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function enableLazyServicesSupport(ContainerBuilder $container)
    {
        $container->setProxyInstantiator(new RuntimeInstantiator());
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadContextRegistry(ContainerBuilder $container)
    {
        $container->setDefinition('sylius_multi_container.context_registry', new Definition(ContextRegistry::class));
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadScopeManipulator(ContainerBuilder $container)
    {
        $definition = new Definition(ScopeManipulator::class, [new Reference('service_container')]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG);

        $container->setDefinition('sylius_multi_container.scope_manipulator', $definition);
    }
}
