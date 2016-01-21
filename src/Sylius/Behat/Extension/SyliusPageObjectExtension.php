<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension;

use Behat\Symfony2Extension\ServiceContainer\Symfony2Extension;
use Behat\Testwork\ServiceContainer\Extension as TestworkExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Sylius\Behat\Extension\Factory\IsolatedSymfonyFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SyliusPageObjectExtension implements TestworkExtension
{
    const DRIVER_KERNEL_ID = 'sylius_extension.kernel';

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'sylius_page_object';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        if (null !== $minkExtension = $extensionManager->getExtension('mink')) {
            $minkExtension->registerDriverFactory(new IsolatedSymfonyFactory());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $container->setDefinition(self::DRIVER_KERNEL_ID, $container->getDefinition(Symfony2Extension::KERNEL_ID));
    }

    /**
     * {@inheritdoc}S
     */
    public function process(ContainerBuilder $container)
    {
        $classNameResolver = $container->get('sensio_labs.page_object_extension.class_name_resolver.camelcased');
        $defaultFactory = $container->get('sensio_labs.page_object_extension.page_factory.default');
        $kernel = $container->get('symfony2_extension.kernel');
        $appContainer = clone $kernel->getContainer();
        $router = $appContainer->get('router');

        $definition = new Definition('Sylius\Behat\Extension\Factory\PageObjectFactory');
        $definition->setArguments(array($classNameResolver, $defaultFactory, new Reference('mink'), $router, '%mink.parameters%'));
        $container->setDefinition('sylius.page_object.factory', $definition);
    }
}
