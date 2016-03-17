<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\SymfonyExtension\ServiceContainer;

use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Symfony2Extension\ServiceContainer\Symfony2Extension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Sylius\Behat\Extension\MultiContainerExtension\ServiceContainer\MultiContainerExtension;
use Sylius\Behat\Extension\SymfonyExtension\Factory\IsolatedSymfonyFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SymfonyExtension implements Extension
{
    /**
     * Kernel used inside Behat contexts or to create services injected to them
     * Container is built before every scenario
     */
    const KERNEL_ID = 'sylius_symfony_extension.kernel';

    /**
     * The current container used in scenario contexts
     * To be used as a factory for current injected application services
     */
    const KERNEL_CONTAINER_ID = 'sylius_symfony_extension.kernel.container';

    /**
     * Kernel used by Symfony2 driver to isolate web container from contexts' container
     * Container is built before every request
     */
    const DRIVER_KERNEL_ID = 'sylius_symfony_extension.driver_kernel';

    /**
     * Kernel that should be used by extensions only
     * Container is built only once at the first use
     */
    const SHARED_KERNEL_ID = 'sylius_symfony_extension.shared_kernel';

    /**
     * The only container built by shared kernel
     * To be used as a factory for shared injected application services
     */
    const SHARED_KERNEL_CONTAINER_ID = 'sylius_symfony_extension.shared_kernel.container';

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'sylius_symfony';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        $this->registerIsolatedSymfonyDriverFactory($extensionManager);
        $this->registerContainers($extensionManager);
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
        $this->aliasSymfony2ExtensionKernel($container);
        $this->declareKernelContainer($container);

        $this->declareDriverKernel($container);

        $this->declareSharedKernel($container);
        $this->declareSharedKernelContainer($container);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * @param ContainerBuilder $container
     */
    private function aliasSymfony2ExtensionKernel(ContainerBuilder $container)
    {
        $container->setAlias(self::KERNEL_ID, Symfony2Extension::KERNEL_ID);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function declareKernelContainer(ContainerBuilder $container)
    {
        $containerDefinition = new Definition(Container::class);
        $containerDefinition->setFactory([
            new Reference(self::KERNEL_ID),
            'getContainer',
        ]);
        $containerDefinition->setScope('scenario');

        $container->setDefinition(self::KERNEL_CONTAINER_ID, $containerDefinition);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function declareDriverKernel(ContainerBuilder $container)
    {
        $container->setDefinition(self::DRIVER_KERNEL_ID, $container->findDefinition(self::KERNEL_ID));
    }

    /**
     * @param ContainerBuilder $container
     */
    private function declareSharedKernel(ContainerBuilder $container)
    {
        $container->setDefinition(self::SHARED_KERNEL_ID, $container->findDefinition(self::KERNEL_ID));
    }

    /**
     * @param ContainerBuilder $container
     */
    private function declareSharedKernelContainer(ContainerBuilder $container)
    {
        $sharedContainerDefinition = new Definition(Container::class);
        $sharedContainerDefinition->setFactory([
            new Reference(self::SHARED_KERNEL_ID),
            'getContainer',
        ]);

        $container->setDefinition(self::SHARED_KERNEL_CONTAINER_ID, $sharedContainerDefinition);
    }

    /**
     * @param ExtensionManager $extensionManager
     */
    private function registerIsolatedSymfonyDriverFactory(ExtensionManager $extensionManager)
    {
        /** @var MinkExtension $minkExtension */
        $minkExtension = $extensionManager->getExtension('mink');
        if (null === $minkExtension) {
            return;
        }

        // Overrides default Symfony2Extension driver factory
        // Have to be registered after that one
        $minkExtension->registerDriverFactory(new IsolatedSymfonyFactory());
    }

    /**
     * @param ExtensionManager $extensionManager
     */
    private function registerContainers(ExtensionManager $extensionManager)
    {
        /** @var MultiContainerExtension $multiContainerExtension */
        $multiContainerExtension = $extensionManager->getExtension('sylius_multi_container');
        if (null === $multiContainerExtension) {
            return;
        }

        $multiContainerExtension->addContainer('symfony', self::KERNEL_CONTAINER_ID);
        $multiContainerExtension->addContainer('symfony_shared', self::SHARED_KERNEL_CONTAINER_ID);
    }
}
