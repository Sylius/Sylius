<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\PageObjectExtension\ServiceContainer;

use Behat\Symfony2Extension\ServiceContainer\Symfony2Extension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Sylius\Behat\PageObjectExtension\Factory\PageObjectFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PageObjectExtension implements Extension
{
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
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $router = $this->extractSymfonyApplicationRouter($container);

        $container
            ->register('sylius.page_object.factory', PageObjectFactory::class)
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
        $applicationKernel = $container->get(Symfony2Extension::KERNEL_ID);
        $applicationContainer = $applicationKernel->getContainer();

        return clone $applicationContainer;
    }
}
