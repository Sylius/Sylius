<?php

namespace Sylius\Behat\SymfonyPageObjectExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Sylius\Behat\SymfonyPageObjectExtension\Factory\SymfonyPageObjectFactory;
use Sylius\Behat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SymfonyPageObjectExtension implements Extension
{
    public function getConfigKey()
    {
        return 'sylius_symfony_page_object';
    }

    public function initialize(ExtensionManager $extensionManager)
    {

    }

    public function configure(ArrayNodeDefinition $builder)
    {

    }

    public function load(ContainerBuilder $container, array $config)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $router = $this->extractSymfonyApplicationRouter($container);

        $container
            ->register('sylius.page_object.factory', SymfonyPageObjectFactory::class)
            ->setArguments([
                new Reference('sylius.page_object.factory.inner'),
                new Reference('mink'),
                new Reference('sensio_labs.page_object_extension.class_name_resolver'),
                '%sensio_labs.page_object_extension.page_factory.page_parameters%',
                $router,
            ])
            ->setDecoratedService('sensio_labs.page_object_extension.page_factory')
            ->setPublic(false)
        ;
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return RouterInterface
     */
    private function extractSymfonyApplicationRouter(ContainerBuilder $container)
    {
        return $this->extractSymfonyApplicationContainer($container)->get('router');
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return ContainerInterface
     */
    private function extractSymfonyApplicationContainer(ContainerBuilder $container)
    {
        $applicationKernel = $container->get(SymfonyExtension::KERNEL_ID);
        $applicationContainer = $applicationKernel->getContainer();

        return clone $applicationContainer;
    }
}
